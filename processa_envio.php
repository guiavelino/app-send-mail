<?php
    require './Bibliotecas/PHPMailer/Exception.php';
    require './Bibliotecas/PHPMailer/OAuth.php';
    require './Bibliotecas/PHPMailer/PHPMailer.php';
    require './Bibliotecas/PHPMailer/POP3.php'; //Contém as especificações do protocolo de recebimento de e-mails.
    require './Bibliotecas/PHPMailer/SMTP.php'; //Contém as especificações do protocolo de envio de e-mails.

    //Importação dos namespaces que serão responsáveis pelas configurações dos envios de e-mail.
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;



    class Mensagem
    {
        private $para = null;
        private $assunto = null;
        private $mensagem = null;
        public $status = ['codigo_status' => null, 'descricao_status' => ''];

        public function __get($atributo)
        {
            return $this->$atributo;
        }

        public function __set($atributo, $valor)
        {
            $this->$atributo = $valor;
        }

        public function mensagemValida()
        {
            //A função empty verifica se o atributo em questão tem o valor vazio.
            if (empty($this->para) || empty($this->assunto) || empty($this->mensagem)) {
                return false;
            } 
            return true;
        }
    }

    $mensagem = new Mensagem();
    $mensagem->para = $_POST['para'];
    $mensagem->assunto = $_POST['assunto'];
    $mensagem->mensagem = $_POST['mensagem'];

    if (!$mensagem->mensagemValida()) {
        echo 'A mensagem não é valida';
        header('Location: index.php');
    }

    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = false;                                                                       // Enable verbose debug output
        $mail->isSMTP();                                                                                // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                                                                 // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                                                         // Enable SMTP authentication
        $mail->Username = 'seu.email@gmail.com';                                                        // SMTP username
        $mail->Password = 'suasenha';                                                                   // SMTP password
        $mail->SMTPSecure = 'tls';                                                                      // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                                                              // TCP port to connect to

        //Recipients
        $mail->setFrom('seu.email@gmail.com', 'Nome do remetente');                                     //Remetente
        $mail->addAddress($mensagem->para);                                                             //Destinatário
        //$mail->addReplyTo('info@example.com', 'Information');                                         //e-mail de recebimento default, caso o destinatário reponda o e-mail do remetente
        // $mail->addCC('cc@example.com');                                                              //e-mail de cópia
        // $mail->addBCC('bcc@example.com');                                                            //e-mail de cópia oculta

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');                                                //Possibilidade de adicionar anexos aos e-mails   

        //Content
        $mail->isHTML(true);                                                                            // Set email format to HTML
        $mail->Subject = $mensagem->assunto;                                                            //Assunto do e-mail
        $mail->Body    = $mensagem->mensagem;                                                           //Corpo do e-mail, suporta tags HTML
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';                  //Corpo do e-mail, não suporta tags HTML

        $mail->send();

        $mensagem->status['codigo_status'] = 1;
        $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso!';
    } 
    catch (Exception $e) 
    {
        $mensagem->status['codigo_status'] = 2;
        $mensagem->status['descricao_status'] = 'Não foi possível enviar esse e-mail! por favor tente novamente mais tarde. Detalhes do erro: ' . $mail->ErrorInfo;
    }

?>

<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>App Mail Send</title>

    	<link rel="stylesheet" href="Bibliotecas/Bootstrap/css/bootstrap.min.css">
	</head>

	<body>

        <div class="containetr">
            <div class="py-3 text-center">
                <img class="d-block mx-auto mb-2" src="Imagens/logo.png" alt="" width="72" height="72">
                <h2>Send Mail</h2>
                <p class="lead">Seu app de envio de e-mails particular!</p>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <?php if($mensagem->status['codigo_status'] == 1){ ?>
                        <div class="container">
                            <h1 class="display-4 text-success">Suceeso</h1>
                            <p> 
                                <?= $mensagem->status['descricao_status'] ?> 
                            </p>
                            <a href="index.php" class="btn btn-success btn-lg mb-5 text-white">Voltar</a>
                        </div>
                    <?php } ?>

                    <?php if($mensagem->status['codigo_status'] == 2){ ?>
                        
                        <div class="container">
                            <h1 class="display-4 text-danger">Ops...</h1>
                            <p> 
                                <?= $mensagem->status['descricao_status'] ?> 
                            </p>
                            <a href="index.php" class="btn btn-danger btn-lg mb-5 text-white">Voltar</a>
                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>

	</body>
</html>