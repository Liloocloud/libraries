<?php
/**
 * Responsável por enviar ler as requisições
 * @copyright Felipe Oliveira Lourenço - 11.04.2021
 * @version 1.0.0
 * Tipos de requisições
 * GET, POST, PUT, PATCH, DELETE, COPY, HEAD, OPTIONS
 * LINK, UNLINK, PURGE, LOCK, UNLOCK, PROPFIND, VIEW
 */

namespace Request;

class Request
{

    private $method; // Guarda o método da requisição para comparar depois
    private $allow; // Método que irá permitir na entrada
    private $header; // Guardo o cabeçalho das requisições

    /**
     * Defini o método aceito na instancia do objeto
     */
    public function __construct($type)
    {
        $this->allow = $type;
        $this->method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
    }

    /**
     * Retorna o método requisitado
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Verifica se o método permitir corresponde
     */
    public function checkType()
    {
        if ($this->method === $this->allow) {
            return true;
        }
        return false;
    }

    /**
     * Recebe o cabeçalho das requisições
     */
    public function getHeader()
    {
        
    }


}
