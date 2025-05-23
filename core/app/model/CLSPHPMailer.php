<?php
require 'plugins/PHPMailer/src/Exception.php';
require 'plugins/PHPMailer/src/PHPMailer.php';
require 'plugins/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CLSPHPMailer
{

	private $host    = "smtp.gmail.com";
	private $dominio = "gmail.com";
	private $de      = "botica.au";
	private $usuario = "botica.au@gmail.com";	
	private $clave   = "kkon sgwz kipa koow"; //sbfi twaq trmc yuef
	private $tituloCorreoMSG = "BOTICA ALFONZO UGARTE";
	private $fromname = 'AVISOS';
	private $objMail;

	public function __CONSTRUCT()
	{
		$this->objMail   = new PHPMailer();
		$this->usuario = $this->de . "@" . $this->dominio;
	}
	
	/**
	 * fnMail
	 * @param array $arraddress
	 * @param array $arrAddcc
	 * @param string $asunto
	 * @param string $cuerpohTML
	 * @param string $pie
	 * @param string $firma
	 * @param array|null $atachar
	 * @return bool
	 */

	public function fnMail($arraddress, $arrAddcc, $asunto, $cuerpohTML, $pie = 'pie', $firma = '', $atachar = null)
    {
        try {
            // Configuración SMTP
            $this->objMail->isSMTP();
            $this->objMail->Host = $this->host;
            $this->objMail->SMTPAuth = true; // Cambiado a true para Gmail
            $this->objMail->Username = $this->usuario;
            $this->objMail->Password = $this->clave;
            $this->objMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usar constante
            $this->objMail->Port = 587;
            $this->objMail->Timeout = 30;
            $this->objMail->SMTPKeepAlive = true;
            $this->objMail->CharSet = 'UTF-8';
            
            // Remitente
            $this->objMail->setFrom($this->usuario, $this->fromname);
            $this->objMail->FromName = $this->fromname;
            
            // Destinatarios
            foreach ($arraddress as $email) {
                $this->objMail->addAddress(trim($email));
            }
            
            // CC
            foreach ($arrAddcc as $email) {
                $this->objMail->addCC(trim($email));
            }
                        
            // Contenido
            $this->objMail->isHTML(true);
            $this->objMail->Subject = $asunto;
            
            // Pie de página (imagen incrustada)
            if ($pie == 'pie') {
                $this->objMail->addEmbeddedImage('img/pie.jpg', 'pie');
            } elseif ($pie == 'pie2') {
                $this->objMail->addEmbeddedImage('img/pie2.jpg', 'pie');
            } elseif ($pie == 'pie3') {
                $this->objMail->addEmbeddedImage('img/pie3.jpg', 'pie');
            }
            
            // Cuerpo del mensaje
            $cuerpo = $this->fn_Cabecera();
            $cuerpo .= $cuerpohTML;
            $cuerpo .= $this->fn_pie($firma);
            $this->objMail->Body = $cuerpo;
            
            // Adjuntos
            if ($atachar) {
                foreach ($atachar as $key => $value) {
                    $this->objMail->addAttachment($key, $value);
                }
            }
            
            // Envío
            if (!$this->objMail->send()) {
                throw new Exception("Error al enviar: " . $this->objMail->ErrorInfo);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $e->getMessage());
            return false;
        } finally {
            $this->objMail->smtpClose();
        }
    }

	public function fn_Cabecera()
	{
		$cuerpo = $this->fn_estilo();
		$cuerpo .= " <body>";
		$cuerpo .= 		" <table>";
		$cuerpo .= 			" <tbody>";
		$cuerpo .= 				" <tr>";
		$cuerpo .= 					" <td valign='top' width='980'>";
		$cuerpo .= 						" <div style='padding: 0px 0px 0px 15px;'>";
		$cuerpo .= 							"<b>Estimado(a):</b>";
		return $cuerpo;
	}

	public function fn_pie($firma = '')
	{
		$cuerpo  = 							" <br/>";
		$cuerpo .= 							" <br/>";
		$cuerpo .= 						" </div>";
		$cuerpo .= 					" </td>";
		$cuerpo .= 				" </tr>";
		$cuerpo .= 				" <tr>";
		$cuerpo .= 					" <td valign='top' width='980'>";
		$cuerpo .= 						" <img src='cid:pie' />";
		$cuerpo .= 					" </td>";
		$cuerpo .= 				" </tr>";
		$cuerpo .= 				$firma;
		$cuerpo .= 			" </tbody>";
		$cuerpo .= 		" </table>";
		$cuerpo .= " </body>";
		return $cuerpo;
	}

	public function fn_estilo()
	{	
		$estilo = "<style  type='text/css'>";

		$estilo .= 		" table{ ";
		$estilo .= 			" font-family: Verdana, sans-serif; ";
		$estilo .= 			" font-size: 9.0pt; ";
		$estilo .= 		" } ";

		$estilo .= 		" #lista{ ";
		$estilo .= 			" margin:0px;padding:0px; ";
		$estilo .= 			" width:100%; ";
		$estilo .= 			" box-shadow: 10px 10px 5px #888888; ";
		$estilo .= 			" border:1px solid #ccc; ";

		$estilo .= 			" -moz-border-radius-bottomleft:0px; ";
		$estilo .= 			" -webkit-border-bottom-left-radius:0px; ";
		$estilo .= 			" border-bottom-left-radius:0px; ";

		$estilo .= 			" -moz-border-radius-bottomright:0px; ";
		$estilo .= 			" -webkit-border-bottom-right-radius:0px; ";
		$estilo .= 			" border-bottom-right-radius:0px; ";

		$estilo .= 			" -moz-border-radius-topright:0px; ";
		$estilo .= 			" -webkit-border-top-right-radius:0px; ";
		$estilo .= 			" border-top-right-radius:0px; ";

		$estilo .= 			" -moz-border-radius-topleft:0px; ";
		$estilo .= 			" -webkit-border-top-left-radius:0px; ";
		$estilo .= 			" border-top-left-radius:0px; ";
		$estilo .= 		" } ";

		$estilo .= 		" #lista tbody tr, #lista tbody tr td{ ";
		$estilo .= 			" border:1px solid #ccc; ";
		$estilo .= 		" } ";

		$estilo .= 		" #lista thead th { ";
		$estilo .= 			" padding: 3px; ";
		$estilo .= 			" mso-ignore: padding; ";
		$estilo .= 			" color: white; ";
		$estilo .= 			" font-size: 9.0pt; ";
		$estilo .= 			" font-weight: 400; ";
		$estilo .= 			" font-style: normal; ";
		$estilo .= 			" text-decoration: none; ";
		$estilo .= 			" font-family: Verdana, sans-serif; ";
		$estilo .= 			" mso-font-charset: 0; ";
		$estilo .= 			" mso-number-format: General; ";
		$estilo .= 			" text-align: center; ";
		$estilo .= 			" vertical-align: middle; ";
		$estilo .= 			" border: 1.0pt solid #CCCCCC; ";
		$estilo .= 			" background: #6699CC; ";
		$estilo .= 			" mso-pattern: black none; ";
		$estilo .= 			" white-space: normal; ";
		$estilo .= 		" } ";

		$estilo .= 		" .NOMBRE { ";
		$estilo .= 			" padding: 0px 0px 0px 15px; ";
		$estilo .= 			" mso-ignore: padding; ";
		$estilo .= 			" color: #E46C0A; ";
		$estilo .= 			" font-size: 9.0pt; ";
		$estilo .= 			" font-weight: 400; ";
		$estilo .= 			" font-style: normal; ";
		$estilo .= 			" text-decoration: none; ";
		$estilo .= 			" font-family: Verdana, sans-serif; ";
		$estilo .= 			" mso-font-charset: 0; ";
		$estilo .= 			" mso-number-format: General; ";
		$estilo .= 			" text-align: general; ";
		$estilo .= 			" vertical-align: middle; ";
		$estilo .= 			" mso-background-source: auto; ";
		$estilo .= 			" mso-pattern: auto; ";
		$estilo .= 			" white-space: nowrap; ";
		$estilo .= 		" } ";

		$estilo .= 		" .sub_pie { ";
		$estilo .= 			" padding: 0px 0px 0px 15px; ";
		$estilo .= 			" mso-ignore: padding; ";
		$estilo .= 			" color: #0e3884; ";
		$estilo .= 			" font-size: 8.0pt; ";
		$estilo .= 			" font-weight: bold; ";
		$estilo .= 			" font-style: italic; ";
		$estilo .= 			" text-decoration: none; ";
		$estilo .= 			" font-family: Verdana, sans-serif; ";
		$estilo .= 			" mso-font-charset: 0; ";
		$estilo .= 			" mso-number-format: General; ";
		$estilo .= 			" text-align: general; ";
		$estilo .= 			" vertical-align: middle; ";
		$estilo .= 			" mso-background-source: auto; ";
		$estilo .= 			" mso-pattern: auto; ";
		$estilo .= 			" white-space: nowrap; ";
		$estilo .= 		" } ";	
		
		$estilo .= " </style>";

		return $estilo;
	}
}