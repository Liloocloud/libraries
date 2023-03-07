<?php
/**
 * Classe genérica para realizar leituras no banco de dados
 * @copyright Felipe Oliveira Lourenço - 23.01.2023
 * @version 1.0.0
 */
namespace Generic;

use Helpers\Pagination;

class Read
{

    private $sql;
    private $where = "";
    private $table;
    private $limit = 10;
    private $navegation = "";

    public function __construct($table, $where = null)
    {
        $this->table = $table;
        $this->where = (isset($where)) ? $where : $this->where;
        $this->sql = "SELECT * FROM `" . $table . "`";
    }

    /**
     * Busca registro pela sintaxe passada pelo parametro. Ideal para montar classes de abstração de terceiros
     *
     * @param String $inner_join - Parte da sintaxe WHERE que contem o Inner Join
     * @param Int|null $limit - Limite de resultados da paginação
     * @param String $statement - Statement caso possua
     * @return Array
     */
    public function join(string $inner_join, int $limit = null, string $statement = '')
    {        
        $limit = ($limit == null) ? $this->limit : $limit;
        $Stx = new Pagination(
            $this->table, '*', $inner_join, $this->limit, null, $statement
        );
        if ($Stx->Results()['bool']) {
            $this->navegation = $Stx->Nav();
            return [
                'bool' => true,
                'message' => 'Resultados encontrados',
                'output' => $Stx->Results()['output'],
            ];
        }
        $this->navegation = '';
        return [
            'bool' => false,
            'message' => 'Nenhum resultado encontrado',
            'output' => null,
        ];
    }

    /**
     * Retorna os valores pelo array de condição "where"
     * @param Array Monta o array como condição where
     * @param Int Número de resultados por página
     */
    public function getArray(array $array, int $limit = null)
    {
        $limit = ($limit == null) ? $this->limit : $limit;
        $w = $this->mountWhere($array)['w'];
        $s = $this->mountWhere($array)['s'];
        $List = new Pagination($this->table, '*', $w, $limit, null, $s);
        if ($List->Results()['bool']) {
            $this->navegation = $List->Nav();
            return [
                'bool' => true,
                'message' => 'Resultados encontrados',
                'output' => $List->Results()['output'],
            ];
        }
        $this->navegation = '';
        return [
            'bool' => false,
            'message' => 'Nenhum resultado encontrado',
            'output' => null,
        ];
    }

    /**
     * Obterm todos as linhas da tabela indica na instancia. Já possui paginação
     * onde o limite de resultados por página é obtido pelo parametro
     * @param Int Número de resultados por página
     */
    public function getAll($limit = null)
    {
        $limit = ($limit == null) ? $this->limit : $limit;
        $List = new Pagination($this->table, "*", null, $limit, null);
        if ($List->Results()['bool']) {
            $this->navegation = $List->Nav();
            return [
                'bool' => true,
                'message' => $List->Results()['message'],
                'output' => $List->Results()['output'],
            ];
        }
        $this->navegation = '';
        return [
            'bool' => true,
            'message' => 'Mensagem',
            'output' => null,
        ];
    }

    /**
     * Retorna o total de linhas pelo Campo passado. O parametro pode ser string ou array
     * @param Array,String Coluna que será selecionada
     * @param Array Campos da condição where
     */
    public function countDataFields($arrayOrString, $where = null)
    {
        $Fields = '';
        if (is_string($arrayOrString)) {
            $Fields = "`{$arrayOrString}`";
        } elseif (is_array($arrayOrString)) {
            foreach ($arrayOrString as $Field) {
                $Fields .= "`{$Field}`,";
            }
            $Fields = substr($Fields, 0, -1);
        }
        $w = $this->mountWhere($where)['w'];
        $s = $this->mountWhere($where)['s'];
        $Count = _get_data_full("SELECT COUNT($Fields) as Total FROM `" . $this->table . "` {$w}", "{$s}");
        $Count = (isset($Count[0]['Total'])) ? $Count[0]['Total'] : 0;
        if ($Count) {
            return [
                'bool' => true,
                'message' => 'Total de resultados',
                'output' => $Count,
            ];
        }
        return [
            'bool' => false,
            'message' => 'Nenhum resultado encontrado',
            'output' => null,
        ];
    }

    /**
     * Obtem informações da tabela indica com base no termo de busca. O ideal é que as colunas
     * indicadas sejam do tipo FULL TEXT para melhorar a performance
     * @param String Termo de busca
     * @param Array Colunas da tabela que deseja coletar informações
     * @param Int Número de resultados por página
     */
    public function getTerms($term, $fields, $limit = null)
    {
        $term = (is_string($term)) ? trim($term) : '';
        if (is_array($fields)) {
            $C = '';
            foreach ($fields as $f) {
                $C .= "`{$f}`,";
            }
            $C = substr($C, 0, -1);
        }
        $limit = ($limit == null) ? $this->limit : $limit;
        $List = new Pagination(
            $this->table,
            '*',
            "WHERE CONCAT_WS(' ',{$C}) LIKE '%{$term}%'",
            $limit
        );
        if ($List) {
            $this->navegation = $List->Nav();
            return [
                'bool' => true,
                'message' => "Resultados de busca com o termo \"{$term}\"",
                'output' => $List->Results()['output'],
            ];
        }
        $this->navegation = '';
        return [
            'bool' => false,
            'message' => "Nenhum resultado com o termo \"{$term}\"",
            'output' => null,
        ];

    }

    /**
     * Retorna linhas com um intervalor de data já com páginação (antiga dateDiff())
     * @param Timestamp Data Inicial de comparação formato '2021-09-02 10:05:04'
     * @param Timestamp Data final de comparação por padrão obtem a data atual
     * @param Array Monta o array como condição where
     * @param Int Número de resultados por página
     */
    public function getBetween($field, $dateInit, $dateFinal = 'NOW()', $where = null, $limit = null)
    {
        $limit = ($limit == null) ? $this->limit : $limit;
        if ($where) {
            $w = $this->mountWhere($where)['w'] . " AND  `{$field}` BETWEEN '{$dateInit}' AND '{$dateFinal}'";
            $s = $this->mountWhere($where)['s'];
        } else {
            $w = "WHERE `{$field}` BETWEEN '{$dateInit}' AND '{$dateFinal}'";
            $s = '';
        }
        $List = new Pagination($this->table, "*", $w, $limit, null, $s);
        if ($List) {
            $this->navegation = $List->Nav();
            return [
                'bool' => true,
                'message' => "Resultado do intervalo de {$this->calculateDays($dateInit, $dateFinal)} dias",
                'output' => $List->Results()['output'],
            ];
        }
        $this->navegation = '';
        return [
            'bool' => false,
            'message' => 'Nenhum resultado encontrado',
            'output' => null,
        ];

    }

    /**
     * Retorna true ou false se os campos passados não foram preenchidos ou são nulos
     * de apenas um registro. Ideal para utilizar em sistema que exijam completar cadastros após login
     * onde é necessário verificar apenas um linha na tabela
     * @param Array Array com os campos que deseja verificar
     */
    public function completeData(array $array)
    {
        $selects = (isset($array['fields'])) ? '`' . implode('`,`', $array['fields']) . '`' : "*";
        $w = $this->mountWhere($array['where'])['w'];
        $s = $this->mountWhere($array['where'])['s'];
        $check = _get_data_full("SELECT {$selects} FROM `{$this->table}` {$w}", "{$s}");
        $check = (isset($check[0])) ? $check[0] : false;
        if ($check) {
            foreach ($check as $key => $val) {
                if ($val == null || $val == '') {
                    return [
                        'bool' => false,
                        'message' => 'Algum campo obrigatório está vazio',
                        'output' => null,
                    ];
                }
            }
            return [
                'bool' => true,
                'message' => 'Dados completos',
                'output' => $check,
            ];
        }
    }

    /**
     * Retorna HTML da paginação para ser utilizado na front-end
     */
    public function Pagination()
    {
        if ($this->navegation) {
            return [
                'bool' => true,
                'message' => 'Paginação HTML',
                'output' => $this->navegation,
            ];
        }
        return [
            'bool' => false,
            'message' => 'Não há paginação',
            'output' => null,
        ];
    }

    /**
     * Verifica se o linha para atualizar existe. ideal para casos onde os dados são mais sensíveis
     * @param Array Sintaxe where para verificação
     */
    public function check($where = null)
    {
        $this->where = ($where == null) ? $this->where : $where;
        $Chk = _get_data_table($this->table, $this->where);
        if ($Chk) {
            return [
                'bool' => true,
                'message' => 'O registro já existe',
                'output' => $Chk,
            ];
        }
        return [
            'bool' => false,
            'message' => 'O registro não existe',
            'output' => null,
        ];
    }

    /**
     * Monta o clausula Where para uso global
     * retornando "where" e "statement"
     * @param Array $array - Array com os valores
     */
    private function mountWhere($array)
    {
        if (is_array($array)) {
            $w = "WHERE ";
            $s = "";
            $i = 1;
            foreach ($array as $key => $value) {
                $w .= "`{$key}` =:a{$i} AND ";
                $s .= "a{$i}={$value}&";
                $i++;
            }
            $w = substr($w, 0, -5);
            $s = substr($s, 0, -1);
            return ['w' => $w, 's' => $s];
        }
        return ['w' => '', 's' => ''];
    }

    /**
     * Calcula a diferença em dias entre as datas passadas
     */
    private function calculateDays($init, $final)
    {
        $final = ($dateFinal = 'NOW()') ? date("Y-m-d H:i:s") : $dateFinal;
        $diff = date_diff(date_create($init), date_create($final));
        return $diff->format("%a");
    }

}
