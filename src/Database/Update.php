<?php
/**
 * Classe responsável por atualizações genéticas no banco de dados!
 * @copyright Felipe Oliveira - 11.01.2017
 * @update - Correção de Bug multiplos update por conta do statements 28.01.2023
 * @version 2.0.1
 */

namespace Database;

use Database\Conn;
use PDOException;
use \PDO;

class Update
{

    private $Tabela;
    private $Dados;
    private $Termos;
    private $Places;
    private $Result;
    private $Update; // @var PDOStatement
    private $Conn; // @var PDO

    /**
     * <b>Exe Update:</b> Executa uma atualização simplificada com Prepared Statments. Basta informar o
     * nome da tabela, os dados a serem atualizados em um Attay Atribuitivo, as condições e uma
     * analize em cadeia (ParseString) para executar.
     * @param STRING $Tabela = Nome da tabela
     * @param ARRAY $Dados = [ NomeDaColuna ] => Valor ( Atribuição )
     * @param STRING $Termos = WHERE coluna = :link AND.. OR..
     * @param STRING $ParseString = link={$link}&link2={$link2}
     */
    public function ExeUpdate($Tabela, array $Dados, $Termos, $ParseString)
    {
        $this->Tabela = (string) "`{$Tabela}`";
        $this->Dados = $Dados;
        $this->Termos = (string) $Termos;
        parse_str($ParseString, $this->Places);
        $this->getSyntax();
        $this->Execute();
    }

    /**
     * <b>Obter resultado:</b> Retorna TRUE se não ocorrer erros, ou FALSE. Mesmo não alterando os dados se uma query
     * for executada com sucesso o retorno será TRUE. Para verificar alterações execute o getRowCount();
     * @return BOOL $Var = True ou False
     */
    public function getResult()
    {
        return $this->Result;
    }

    /**
     * <b>Contar Registros: </b> Retorna o número de linhas alteradas no banco!
     * @return INT $Var = Quantidade de linhas alteradas
     */
    public function getRowCount()
    {
        return $this->Update->rowCount();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */
    //Obtém o PDO e Prepara a query
    private function Connect()
    {
        $this->Conn = Conn::getConn();
        $this->Update = $this->Conn->prepare($this->Update);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax()
    {
        foreach ($this->Dados as $Key => $Value) {
            $Places[] = "`{$Key}`" . ' =:' . $Key;
        }
        $Places = implode(', ', $Places);
        $this->Update = "UPDATE {$this->Tabela} SET {$Places} {$this->Termos}";
    }

    //Obtém a Conexão e a Syntax, executa a query!
    private function Execute()
    {
        $this->Connect();
        $this->setNull();
        try {
            $this->Update->execute(array_merge($this->Dados, $this->Places));
            $this->Result = true;
        } catch (PDOException $e) {
            $this->Result = null;
            PHPErro("<b>Erro ao Ler:</b> {$e->getMessage()}", $e->getCode());
        }
    }

    //Set empty data to NULL
    private function setNull()
    {
        foreach ($this->Dados as $Key => $Value) {
            $this->Dados[$Key] = ($Value == "" ? null : $Value);
        }
    }

}
