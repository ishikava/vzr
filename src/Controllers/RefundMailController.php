<?php

namespace Vzr\Controllers;

use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class RefundMailController extends FranchiseController
{
    protected $params = array(
        'email' => array('required' => true, 'type' => 'string'),
        'key' => array('required' => true, 'type' => 'string')
    );

    public function mail()
    {
        try {
            $oFormValidation = FormValidation::getInstance();
            $this->getParams = $oFormValidation->xss_clean($_POST);
            $check = $this->checkPostFields();
            if ($check && $_SESSION['refund_key'] == urldecode($this->params['key'])) {
                $theme = 'АО Vzr. Заявление на возврат авансового взноса (300 руб.)';
                $mess = 'Добрый день!<br>Заявление на возврат авансового взноса (300 руб.) за прикрепление близких родственников.';

                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->isHTML(true);
                $mail->Host = '10.128.0.44';
                $mail->Port = 25;
                $mail->CharSet = 'UTF-8';
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
                $mail->setFrom('robot@Vzr.ru', 'Vzr');
                $mail->addAddress($this->params['email']);
//                $mail->addAddress('kiselevmg@Vzr.ru');
//                $mail->addAddress('KochikDN@Vzr.ru');

                $attachedFiles = [];

                foreach ($_SESSION['pdfs'] as $pdfkey => $pdfdata) {
                    $fileName = $_SERVER['DOCUMENT_ROOT'] . '/../var/file/' . md5($pdfkey.$pdfdata) . '.pdf';
                    file_put_contents($fileName, base64_decode($pdfdata));
                    $mail->addAttachment($fileName, $pdfkey);
                    $attachedFiles[] = $fileName;
                }

                $mail->Subject = $theme;
                $mail->Body = $mess;
                $mail->send();
                $mail->ClearAddresses();
                $mail->ClearAttachments();

                foreach ($attachedFiles as $file) {
                    unlink($file);
                }

                echo 'Письмо успешно отправлено на адрес ' . $this->params['email'];

            } else {
                echo $this->twig->render('404.twig');
            }
        } catch
        (\Exception $e) {
            echo $this->twig->render('404.twig');
        }
    }

}

