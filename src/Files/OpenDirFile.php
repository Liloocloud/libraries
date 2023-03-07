<?php
/**
 * Classe para navegar em arquivos, pastas e subpastas com a finalidade de retornar o nome dos arquivos e pasta
 * @copyright felipe_tecnologia@hotmail.com - Central 7 develop - 29.07.2015 18:15
 * @version 2.1.0
 *
 */
namespace Liloo\Files;

class OpenDirFile
{

    private $path; // Caminho ou pasta
    private $type = [
        'jpg', 'php', 'tpl',
        'html', 'js', 'css',
        'json',
    ]; // Extenssões permitidas por padrao jpg
    private $file; // Armazena o nome do arquivo a ser trabalhado
    private $file_content; // Guarda o conteudo do arquivo (se array ou não)
    private $message = []; // Retorna as mensagens da classe

    public function __construct(string $path = null)
    {
        if (isset($path)) {
            $this->path = (is_dir($path)) ? $path : array_push($this->message, 'O caminho passado não é um diretório');
        } else {
            array_push($this->message, 'O 1º parâmetro é obrigatório');
        }
    }

    /**
     * Seta os tipo de arquivo que deseja abrir
     * @param  [type] $type [description]
     */
    public function type($type)
    {
        $this->type = $type;
    }

    /**
     * Retorna o numero de arquivos (somente arquivos) de uma pasta
     */
    public function numArquivos()
    {
        return count(glob($this->path . "*." . $this->type));
    }

    /**
     * Retorna uma lista de somente arquivos do diretório
     * lendo as extensões permitidas
     */
    public function listFiles()
    {
        return glob($this->path . "*." . $this->type);
    }

    /**
     * Retorna o número de arquivos e pastas do diretório
     */
    public function count(): int
    {
        $con = count(scandir($this->path)) - 2;
        if ($con !== -1):
            return $con;
        else:
            array_push($this->message, 'Nenhum arquivo encontrado');
        endif;
    }

    /**
     * Retorna um array dos arquivos e pastas do diretorio passado
     */
    function list(): array
    {
        $list = scandir($this->path);
        $key = array_search('..', $list);
        if ($key !== false) {
            unset($list[0], $list[1]);
        }
        return $list;
    }

    /**
     * Lista apenas os diretórios do diretório passado
     * @return [type] [description]
     */
    public function listDir()
    {
        $listDir = [];
        foreach ($this->list() as $dir) {
            if (is_dir($this->path . $dir)) {
                array_push($listDir, $dir);
            }
        }
        return $listDir;
    }

    /**
     * Método que abre arquivo
     */
    public function openFile()
    {
        return $this->file_content = file($this->file);
    }

    /**
     * Método que lê todas as linhas do arquivo
     */
    public function contentFile(): string
    {
        $file = file($this->file);
        for ($i = 0; $i < count($file); $i++) {
            return utf8_encode($this->file_content[$i]);
        }
    }

    /**
     * Lê o arquivo (Se parametro vazio lê a linha 1)
     */
    public function readLineFile($lin): string
    {
        //Verifica se $lin esta vazio
        $lin = ($lin == "") ? $lin = 0 : $lin;
        return utf8_encode($this->file_content[$lin]);
    }

    /**
     * Lê o arquivo e retorna seu conteúdo
     */
    public function readFile()
    {
        $filename = $this->getArquivo();
        $handle = fopen($filename, "r");
        return fread($handle, filesize($filename));
    }

    /**
     * Fecha o ponteiro do arquivo
     */
    public function closeFile()
    {
        fclose($this->file);
    }
}
