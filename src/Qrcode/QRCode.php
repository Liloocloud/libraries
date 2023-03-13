<?php
/**
 * Auxiliar de Criação do QRCode 
 * @copyright 13.03.2023 Felipe Oliveira Lourenço
 */

namespace Liloo\Qrcode;

class QRCode
{
    private static $content; // Conteudo o QRcode
    private static $type; // Tipo do QRcode (Email, vCard, Site, Skype e etc.)
    private static $path = ROOT_UPLOADS . 'qrcodes/'; // caminho ROOT para salvar o arquivo PNG
    private static $png =  BASE_UPLOADS . 'qrcodes/'; // Caminho WEB para visualizar o QRcode

    /**
     * Monta o vCard
     *
     * @param Array $data - Array com os Valores do vCard
     * @param Int $mg - Margem final da imagem PNG
     * @param Inte $size - Tamanho do QR Code
     * @param Define $level - Nível de correção de Erro QR_ECLEVEL_L = 7%, QR_ECLEVEL_M = 15%, QR_ECLEVEL_Q = 25% e QR_ECLEVEL_H = 30%  
     * @return void
     */
    public static function vCard(array $data, $mg = 0, $size = 3, $level = QR_ECLEVEL_L)
    {
        $QR = 'BEGIN:VCARD' . "\n";
        $QR .= 'VERSION:3.0' . "\n";
        // $QR .= 'N:'.$sortName."\n";
        $QR .= 'FN:' . $data['name'] . "\n";
        $QR .= 'ORG:' . $data['org'] . "\n";
        // $QR .= 'TITLE:' . $data['org'] . "\n";
        $QR .= 'TEL;WORK;VOICE:' . $data['phone'] . "\n";
        // $QR .= 'TEL;HOME;VOICE:'.$fonecasa."\n";
        $QR .= 'TEL;TYPE=cell:' . $data['celular'] . "\n";
        $QR .= 'EMAIL:' . $data['email'] . "\n";
        $QR .= 'URL;TYPE=work:' . $data['site'] . "\n";
        $QR .= 'ADR;TYPE=work;' . 'LABEL="' . $data['address_label'] . '":'
            // . $addressPobox . ';'
            // . $addressExt . ';'
            . $data['address_street'] . ';'
            . $data['address_city'] . ' - ' . $data['address_region'] . ';'
            . $data['address_zipcode'] . ';'
            . $data['address_country']
            . "\n";
        $QR .= 'REV:' . date("Ymd") . "\n";
        $QR .= 'END:VCARD';

        Render::png($QR, self::$path . 'qr-' . $data['id'] . '.png', $level, $size, $mg);
        return self::$png . 'qr-' . $data['id'] . '.png';
    }
}