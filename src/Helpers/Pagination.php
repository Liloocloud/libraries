<?php
/**
 * Controla o sistema de paginação completa com todos os links em Uikit
 * @copyright Felipe Oliveira - 11.06.2022
 * @version 2.0.0
 */

namespace Helpers;

class Pagination
{
    private $table; // Tabela do banco que será utilizada
    private $select; // Campos do Select podendo ser o que quiser Ex.: Count(), Distinct  e etc.
    private $where; // Condição WHERE do MySql
    private $limit = 10; // Limite de resulatados por página
    private $page; // Página atual dos resultados
    private $url; // URL atual, serve para repor a url na paginação HTML

    private $total; // Retorna o total de resultados da busca
    private $totalPages; // Total de páginas para o resultado
    private $offset = 0; // Ajuda na Sintaxe SQL LIMIT OFFSER
    private $results; // Guarda o resultado da pesquisa já paginada

    public function __construct(string $table, $select = '*', string $where = null, $limit = null, $page = null, $statement = '')
    {
        $this->limit = ($limit != null) ? $limit : $this->limit;
        $this->page = ($page == null) ? filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) : $page;
        $this->page = ($this->page <= 0) ? 1 : $this->page;
        $this->offset = ($this->page - 1);
        $this->where = ($where != null) ? $where : '';
        $total = _get_data_full("SELECT Count(*) AS total FROM `" . $table . "` {$this->where}", $statement);
        $this->total = (isset($total[0]['total'])) ? (int) $total[0]['total'] : 0;
        $this->select = $select;      
        
        $this->results = _get_data_full("SELECT {$this->select} FROM `" . $table . "` {$this->where} LIMIT {$this->limit} OFFSET " . ($this->limit * $this->offset), $statement);
        
        $this->totalPages = (int) ceil($this->total / $this->limit);
        $this->totalPages = (!is_float($this->totalPages)) ? $this->totalPages : 0;
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (strpos($url, '&page=') != false) {
            $url = explode('&page=', $url)[0];
            $this->url = $url . '&';
        } elseif (strpos($url, '?page=') != false) {
            $url = explode('?page=', $url)[0];
            $this->url = $url . '?';
        } elseif (strpos($url, '?') != false) {
            $this->url = $url . '&';
        } else {
            $this->url = $url . '?';
        }
    }

    /**
     * Retorna o HTML da paginação (Compatível com o UIKIT)
     * já com os cáculos de paginação
     */
    public function Nav()
    {
        if($this->totalPages <= 1){
            return '';
        }
        if ($this->results()['bool']) {
            $view = '<ul class="uk-pagination uk-flex uk-uk-flex-center">';

            // Anterior
            if ($this->page <= 1) {
                $view .= '<li class="uk-disabled fix"><a href="">Anterior</a></li>';
            } else {
                $view .= '<li class="fix"><a href="' . $this->url . 'page=' . ($this->page - 1) . '">Anterior</a></li>';
            }

            // Primeira página
            if ($this->page == 1) {
                $view .= '<li class="uk-disabled uk-visible@m"><a href="">1</a></li>';
            } else {
                $view .= '<li class="uk-visible@m"><a href="' . $this->url . 'page=1">1</a></li>';
            }

            // Separador
            $view .= '<li class="uk-disabled uk-visible@m"><span>...</span></li>';

            // 3 Anteriores
            if ($this->page) {
                for ($i = 3; $i >= 1; $i--) {
                    $n[$i] = $this->page - $i;
                    if ($n[$i] > 1) {
                        $view .= '<li class="uk-visible@m"><a href="' . $this->url . 'page=' . $n[$i] . '">' . $n[$i] . '</a></li>';
                    }
                }
            }

            // Página atual
            if ($this->page) {
                $view .= '<li class="uk-active uk-visible@m"><span>' . $this->page . '</span></li>';
            }

            // 3 Próximas
            if ($this->page) {
                for ($i = 1; $i <= 3; $i++) {
                    $n[$i] = $this->page + $i;
                    if ($n[$i] < $this->totalPages) {
                        $view .= '<li clas="uk-visible@m"><a href="' . $this->url . 'page=' . $n[$i] . '">' . $n[$i] . '</a></li>';
                    }
                }
            }

            // Separador
            $view .= '<li class="uk-disabled uk-visible@m"><span>...</span></li>';

            // última página
            if ($this->page == $this->totalPages) {
                $view .= '<li class="uk-disabled uk-visible@m"><a href="">' . $this->totalPages . '</a></li>';
            } else {
                $view .= '<li class="uk-visible@m"><a href="' . $this->url . 'page=' . ($this->totalPages) . '">' . $this->totalPages . '</a></li>';
            }

            // Próxima
            if ($this->page <= ($this->totalPages - 1)) {
                $view .= '<li class="fix"><a href="' . $this->url . 'page=' . ($this->page + 1) . '">Próximo</a></li>';
            } else {
                $view .= '<li class="uk-disabled fix"><a href="">Próximo</a></li>';
            }

            return $view;
        }
    }

    /**
     * Retorna todos os valores do banco
     * já piginados referente a pesquisa
     */
    public function Results()
    {
        if (!empty($this->results)) {
            $Msg = (count($this->results) > 1) ? 'Resultados encontrados' : 'Resultado encontrado';
            return [
                'bool' => true,
                'message' => "{$Msg}",
                'output' => $this->results,
            ];
        }
        return [
            'bool' => false,
            'message' => "Nenhum resultado encontrado para esta página",
            'output' => $this->results,
        ];
    }

    /**
     * Retorna o Total de resultados da pesquisa
     */
    public function Total()
    {
        if ($this->total > 0) {
            $Msg = ($this->total == 1) ? $this->total . ' resultado' : $this->total . ' resultados';
            return [
                'bool' => true,
                'message' => "Total de {$Msg}",
                'output' => $this->total,
            ];
        }
        return [
            'bool' => false,
            'message' => "Não há resultados",
            'output' => $this->total,
        ];
    }

    /**
     * Retorna o Total de páginas que o resultado da pesquisa possui
     */
    public function totalPages()
    {
        if ($this->totalPages > 0) {
            $Msg = ($this->totalPages == 1) ? $this->totalPages . ' página' : $this->totalPages . ' páginas';
            return [
                'bool' => true,
                'message' => "Resultado com o total de {$Msg}",
                'output' => $this->totalPages,
            ];
        }
        return [
            'bool' => false,
            'message' => "Não há páginas para essa busca",
            'output' => $this->totalPages,
        ];
    }
}
