<?php
/**
 * Class para abstração e uso da Biblioteca PHPMAiler
 * Tem como objetivo montar o email com as devidas regras
 * para o disparo
 *
 * Esta classe é Nativa da Plataforma e trabalha direto
 * com a função mail() do PHP
 * 
 * @copyright Felipe Oliveira Lourenço - 24.04.2020
 */

namespace Liloo\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use \stdClass;
// require ROOT.'_Kernel/Libs/PHPMailer/vendor/autoload.php';
// require ROOT_THEME."__config.theme.php";

// echo '<pre>';
// print_r(__THEME__);
// echo '</pre>';

class Email
{
	private $mail; 	// Utiliza a Class PHPMailer
	private $data; 	// Prepara o EMail 
	private $error;	// Trata os Erros Exception da Biblioteca
	private $host = __THEME__['mail_host']; // Host do servidor de email
	private $port = __THEME__['mail_port']; // Porta do servidor de email
	private $user = __THEME__['mail_user']; // Usuário do servidor de email
	private $pass = __THEME__['mail_pass']; // Senha do servidor de email

	public function __construct(){
		$this->data = new stdClass();
		$this->mail = new PHPMailer(true);

		// PADRÃO DE MENSAGEM
		$this->mail->isSMTP();
		$this->mail->isHTML(true);
		$this->mail->setLanguage("br");

		// PADRÃO DE AUTENTICAÇAO
		//$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$this->mail->SMTPAuth = true;
		$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$this->mail->CharSet = "utf-8";

		// AUTENTICAÇÃO DO SERVIDOR
		$this->mail->Host = $this->host;
		$this->mail->Port = $this->port;
		$this->mail->Username = $this->user;
		$this->mail->Password = $this->pass;
	}

	/**
	 * Adicionando novo mensagem montando o Email
	 * @param string $subject         [assunto da mensagem]
	 * @param string $body            [corpo da mensagem html]
	 * @param string $recipient_name  [----]
	 * @param string $recipient_email [----]
	 */
	public function add(string $subject, string $body, string $recipient_name, string $recipient_email): Email
	{
		$this->data->subject = $subject;
		$this->data->body = $body;
		$this->data->recipient_name = $recipient_name;
		$this->data->recipient_email = $recipient_email;
		return $this;
	}

	/**
	 * Envia anexo caso seja necessário. Também tem a possibilidade de array
	 * @param  string $file_path [caminho do arquivo]
	 * @param  string $file_name [nome do arquivo]
	 * @return [type]            [description]
	 */
	public function attach(string $file_path, string $file_name): Email
	{
		$this->data->attach[$file_path] = $file_name;
	}

	/**
	 * Dispara o e-mail utilizando por padrão 
	 * @param string $from_name [name de quem está enviando]
	 * @param string $from_email [email de quem está enviando]
	 */
	public function send(string $from_name = __THEME__['mail_from_name'], string $from_email = __THEME__['mail_from_email']): bool
	{
		try{
			// Montando o envio
			$this->mail->Subject = $this->data->subject;
			$this->mail->msgHTML($this->data->body);
			$this->mail->addAddress($this->data->recipient_email, $this->data->recipient_name);
			$this->mail->setFrom($from_email, $from_name);

			// Verificando se possue anexo
			if(!empty($this->data->attach)){
				foreach ($this->data->attach as $path => $name) {
					$this->mail->addAttachment($path, $name);
				}
			}

			$this->mail->send();
			return true;

		}catch (Exception $exception){
			$this->error = $exception;
			return false;
		}
	}

	/** Retorna a Mensagem de Erro (Exception) */
	public function error(): ?Exception	{
		return $this->error;
	}
}