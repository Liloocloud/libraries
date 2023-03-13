# Classes Auxiliares para Liloo Framework PHP
Recursos para auxilio no desenvolvimento de aplicações que utilizando o Framework PHP Liloo

## Recursos

Tratamento do banco de dados
* Liloo\Database\Conn
* Liloo\Database\Create
* Liloo\Database\Delete
* Liloo\Database\Read
* Liloo\Database\Update

Envio de e-mail utilizando PHPmailer
* Liloo\Email\Email

Gestão de arquivos
* Liloo\Files\OpenDirFile

Busca avançado com auxilio de paginação
* Liloo\Generic\Create
* Liloo\Generic\Read

Auxiliares
* LIloo\Helpers\Json
* Liloo\Helpers\Markdown
* Liloo\Helpers\Pagination

Gestão de requisições
* Liloo\Request\cURL
* Liloo\Request\Request

Gerador de QRcode
* Liloo\Qrcode\*

## Implementando QRCode
Nesse exemplo estamos utilizando a Liloo Framework, pois já retornar os dados para incluirmos no array como parametro da classe.
- Instancia
- Array com os Dados
- Renderização do vCard QRCode

```php
use Liloo\Qrcode\Render;
$QRcode = [
    'id' => $Ads['account_id'],
    'name' => "{$Ads['ads_title']} - {$Extra['SITE_NAME']}",
    'org' => $Ads['ads_title'],
    'phone' => $Ads['ads_phone'],
    'celular' => $Ads['ads_whatsapp'],
    'email' => $Ads['ads_email'],
    'site' => $Ads['ads_site'],
    'address_label' => $Ads['account_name'],
    'address_street' => "{$Ads['ads_address']}, {$Ads['ads_address_number']} {$Ads['ads_address_complement']}",
    'address_city' => "{$Ads['ads_address_district']}, {$Ads['ads_address_city']}",
    'address_region' => "{$Ads['ads_address_state']} - {$Ads['ads_address_uf']}",
    'address_zipcode' => $Ads['ads_address_zipcode'],
    'address_country' => 'Brasil',
];
$FilePNG = QRCode::vCard($QRcode);
```
## Requisitos
* Versão mínima do PHP 7+
* PHPMailer para envio de emails
* Framework PHP Liloo
