<?php
/**
 * Responsável por enviar requisições para servidores via cURL
 * @copyright Felipe Oliveira Lourenço - 29.03.2021
 * @version 1.0.0
 */

namespace Liloo;

class cURL
{

    private $url; // URL para onde vai mandar a requisição
    private $header = ['Content-Type: application/x-www-form-urlencoded; charset=UTF-8'];
    private $ssl = true; // Permitir apenas protocolo "HTTPS"
    private $dataType; // Tipo de retorno que está aguardando
    private $method; // Tipo de requisição a ser enviada, se POST, GET, PUT e etc
    private $endpoint; // Array com o endipoint indicado pela API
    private $endpointType; // Tipo de endipoint, se JSON ou XML

    public function __construct($Args = null)
    {
        if ($Args == null) {
            return false;
        }

        $this->url = (isset($Args['url'])) ? $Args['url'] : false;
        $this->method = (isset($Args['method'])) ? $Args['method'] : false;
        $this->ssl = (isset($Args['ssl'])) ? $Args['ssl'] : false;
        $this->dataType = (isset($Args['dataType'])) ? $Args['dataType'] : false;
        $this->endpoint = (isset($Args['endpoint'])) ? $Args['endpoint'] : false;
        $this->endpointType = (isset($Args['endpointType'])) ? $Args['endpointType'] : false;
    }

    /**
     * Envia pela função exec() e retorna o resultado
     */
    public function endpoint()
    {
        return $this->exec();
    }

    /**
     * Executa a requisição em modo privado
     */
    private function exec()
    {
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $Response = curl_exec($curl);
        curl_close($curl);
        $xml = simplexml_load_string($Response);
        return json_encode($xml);
    }
}
