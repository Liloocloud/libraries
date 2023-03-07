<?php
/**
 * Class para gerenciamento e manipulação de arquivos JSON
 * @copyright Felipe Oliveira Lourenço - 04.01.2021
 * @version 1.0.0
 */

namespace Liloo\Helpers;

class Json
{

    /**
     * [encondeJSON description]
     * @param  [type] $json [description]
     * @return [type]       [description]
     */
    public function encondeJSON($json)
    {
        if (!is_array($json)) {
            return false;
        }
        foreach ($json as $value) {
            html_entity_decode($value);
        }
        echo json_encode(
            $json,
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE
        );
    }
}

/**
 * Compilador de Banco para JSON storage, permitindo salva numa pasta expecífica
 * @category _do_
 * @param  [aray]  $Data     [dados array para compilar]
 * @param  [array] $path     [Caminho onde deseja salvar o arquivo]
 * @return [type]           [description]
 */
function _do_json_complile_storage($datatb = null, $namefile = '', $path = ROOT . 'storage/')
{
    //$Data = _get_data_table($table);
    //var_dump($Data);
    //if (!file_exists($path.$namefile)):
    $Data = json_encode($datatb);
    $storage = fopen($path . $namefile, "w");
    fwrite($storage, $Data);
    fclose($storage);
    //endif;
}

/**
 * Lê um arquivo json compilado acima
 * @param  string $filePath [Caminho do arquivo JSON]
 * @return [array]          [retorna os indeces e valores array]
 */
function _do_json_to_array($filePath = '')
{
    if ($filePath != ''):
        $File = file_get_contents($filePath);
        $File = json_decode($File, true);
        return $File;
    else:
        _ERROR('Caminho do arquivo não foi informado');
    endif;
}
