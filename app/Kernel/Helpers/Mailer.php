<?php
/**
 * @author George Azevedo <george@fenix.rio.br>
 * @copyright Copyright (c) 2023 Fênix Comunicação (https://www.fenix.rio.br)
 */
declare(strict_types=1);
namespace App\Kernel\Helpers;

//use App\Kernel\Helpers\{Mailer};

/**
 * Class Mailer
 */
class Mailer
{

    /**
     * Router constructor
     */
    public function __construct()
    {
        $this->process();
    }

    /**
     * mailerProcess
     */
    public function process()
    {
        $valid_post_types = ['lead'];
        if (
            isset($_POST)
            && isset($_POST['type'])
            && in_array(trim($_POST['type']), $valid_post_types)
            && strpos(
                $_SERVER['HTTP_REFERER'],
                $_SERVER['HTTP_HOST']
            ) !== false
        ) {
            $post = $_POST;

            if ($post['type'] == 'lead') {
                $format_validations = true;
                foreach ($post as $post_item) {
                    preg_match('/(http|ftp|mailto)/i', $post_item, $urls);
                    preg_match('/(select\ +.*from|insert\ +.*into|update\ +.*set|delete\ +.*from)/i', $post_item, $injections);

                    if (
                        count($urls) > 0
                        || count($injections) > 0
                    ) {
                        $format_validations = false;
                    }
                }

                //recaptcha php check
                $rec_secret = '';
                $rc_ck = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $rec_secret .
                    '&response=' . $post['grecaptcha'] .
                    '&remoteip=' . $_SERVER['REMOTE_ADDR'];
                $recaptchaReturn = json_decode(file_get_contents($rc_ck), true);

                $required_validations = (
                    $format_validations
                    && isset($post['nome']) && !empty($post['nome'])
                    && isset($post['email']) && !empty($post['email']) && (filter_var($post['email'], FILTER_VALIDATE_EMAIL))
                    && isset($post['tel']) && !empty($post['tel'])
                    && isset($post['grecaptcha']) && !empty($post['grecaptcha'])
                    && $recaptchaReturn['success'] == true && ($recaptchaReturn['score'] > 0.7)
                ) ? true : false;

                if ($required_validations) {

                    foreach ($post as $post_key => $post_item) {
                        $post[$post_key] = (!empty(trim($post_item))) ? $post_item : 'Não informado';
                    }

                    $post = (object) $post;
                    $mail = new \PHPMailer(true);
                    $domain = $_SERVER['HTTP_HOST'];

                    try {
                        //Recipients
                        $mail->setFrom('hrhunter@hrhunter.com.br', 'HR Hunter');
                        if ($post->nome != 'fnxadmin') {
                            $mail->addAddress('george@glab.dev.br', 'Patrícia Ventura');
                            $mail->addBCC('george@glab.dev.br', 'DEV HR Hunter');
                        } else {
                            $mail->addAddress('georgelab@gmail.com', 'DEV HR Hunter');
                        }
                        $mail->addReplyTo($post->email, $post->nome);

                        //Content
                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = 'Contato recebido via site';
                        $message_body = "
                        <h2>Dados informados</h2>
                        <p><b>Nome</b>: {$post->nome}</p>
                        <p><b>Tel/Whatsapp</b>: {$post->tel}</p>
                        <p><b>E-mail</b>: {$post->email}</p>
                        <br />
                        <p><i>O contato aceitou enviar os dados informados acima para a HR Hunter.</i></p>
                        ";
                        $mail->Body = $message_body;
                        $mail->AltBody = strip_tags(str_replace(['</p>', '</h2>'], "\r\n", $message_body));

                        $mail->send();

                    } catch (\Exception $e) {
                        #TODO
                        //$this->logWarn(['title' => 'mailer', $mail->ErrorInfo]);
                    }
                }

            }
        }
    }
}