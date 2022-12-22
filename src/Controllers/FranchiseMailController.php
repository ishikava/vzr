<?php

namespace Vzr\Controllers;

use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class FranchiseMailController extends FranchiseController
{
    protected $params = array(
        'police-id' => array('required' => true, 'type' => 'integer'),
        'police-number' => array('required' => true, 'type' => 'integer'),
        'subj-id' => array('required' => true, 'type' => 'integer'),
        'phone' => array('required' => true, 'type' => 'integer'),
        'sum' => array('required' => true, 'type' => 'string'),
        'key' => array('required' => true, 'type' => 'string'),
        'email' => array('required' => true, 'type' => 'string'),
        'original-email' => array('required' => true, 'type' => 'string')
    );

    public function mail()
    {
        try {
            $oFormValidation = FormValidation::getInstance();
            $this->getParams = $oFormValidation->xss_clean($_POST);
            $check = $this->checkPostFields();
            if ($check && $_SESSION['franchise_key'] == urldecode($this->params['key'])) {
                $service = new ContractService();
                $doc = $service->getFranchiseCertificateDoc([
                    'PolicyId' => intval($this->params['police-id']),
                    'ChildSubjId' => intval($this->params['subj-id']),
                    'Login' => '+7' . substr(intval($this->params['phone']), 1, strlen($this->params['phone']) -1),//Заменить первую цифру телефона на +7
                    'FnsTotalSum' => $this->params['sum']
                ]);

                $fileName = $_SERVER['DOCUMENT_ROOT'] . '/../var/file/' . intval($this->params['police-id']) . '-' . intval($this->params['subj-id']) . '.pdf';
                file_put_contents($fileName, $doc['docPDF']);

                $theme = 'Ваша справка по оплаченной франшизе во вложении';
                $mess = 'Ваша справка по оплаченной франшизе отправлена на почту ' . $this->params['email'];

                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host = '10.128.0.44';
                $mail->Port = 25;
                $mail->CharSet = 'UTF-8';
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
                //Recipients
                $mail->setFrom('robot@Vzr.ru', 'Vzr');
                $mail->addAddress($this->params['email']);
                $mail->addAttachment($fileName);
                $mail->Subject = $theme;
                $mail->Body = $theme;
                $mail->send();
                $mail->ClearAddresses();
                $mail->ClearAttachments();

                unlink($fileName);

                echo $this->twig->render('success-franchise-mail.twig', array('message' => $mess, 'backlink' => '/franchise/?'.$_SESSION['backlink'] ));

            } else {
                echo $this->twig->render('404.twig');
            }
        } catch
        (\Exception $e) {
            echo $this->twig->render('404.twig');
        }
    }

}

