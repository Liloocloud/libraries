<?php
/**
 * Classe genérica para realizar leituras no banco de dados
 * @copyright Felipe Oliveira Lourenço - 23.01.2023
 * @version 1.0.0
 */
namespace Generic;

class Create
{
    private $sql;
    private $where = "";
    private $table;
    private $check;
    private $values;

    public function __construct($table, $where)
    {
        $this->table = $table;
        $this->where = $where;
        // $this->sql = "SELECT * FROM `" . $table . "`";
    }

    /**
     * Adiciona um item na tabela passada na instancia,
     * Caso seja necessário inserir varios rode num loop de repetição
     * @param Array Campos com os valores key=values
     */
    public function insertItem()
    {  
        if($this->where){
            $Chk = $this->check($this->where);            
            if(!$Chk['bool']){
                $Set = _set_data_table($this->table, $this->where);
                return [
                    'bool' => (!empty($Set))? true : false,
                    'message' => $Chk['message'],
                    'output' => (!empty($Set))? ['id' => $Set] : null,
                ];
            }   
            return [
                'bool' => false,
                'message' => 'Já existe um regitro com esse dados',
                'output' => null,
            ];             
        }
        return [
            'bool' => false,
            'message' => 'Valores dos campos não foram passados',
            'output' => null,
        ];
    }

    /**
     * Monta o clausula Where para uso global retornando "where" e "statement"
     * @param Array Condições WHERE para montagem da sintaxe
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
     * @param Date Data inicial que deseja comparar
     * @param Date Data final que deseja comparar
     */
    private function calculateDays($init, $final)
    {
        $final = ($dateFinal = 'NOW()') ? date("Y-m-d H:i:s") : $dateFinal;
        $diff = date_diff(date_create($init), date_create($final));
        return $diff->format("%a");
    }

  /**
     * Verifica se o linha para atualizar existe. ideal para casos onde os dados são mais sensíveis
     * @param Array Sintaxe where para verificação
     */
    private function check($where = null)
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

}
