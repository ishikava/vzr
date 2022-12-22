<?php

namespace Vzr\Controllers;

use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class ContractController
{
    /**
     * @var bool
     */
    private $contractId;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __construct($contractId = null)
    {
        $loader = new FilesystemLoader($_SERVER["DOCUMENT_ROOT"] . '/../templates');
        $this->twig = new Environment($loader, array('debug' => true));
        $this->twig->addExtension(new DebugExtension());
        $oFormValidation = FormValidation::getInstance();
        $this->contractId = $oFormValidation->xss_clean($contractId);
    }

    public function getDraftPage()
    {
        if (!$contract = $this->getContract()) {
            echo $this->twig->render('404.twig');
        } else {
            $service = new ContractService();
            $params = StorageController::get('params');
            $string = http_build_query($params);
            $mess = $service->getMessApp(
                array(
                    'PolicyId' => $this->contractId,
                    'FormId' => 2
                )
            )['MessAppList'];
            echo $this->twig->render('draft.twig', array('data' => $contract, 'mess' => $mess, 'string' => $string));
        }
    }

    public function sendMail()
    {
        if (!$contract = $this->getContract()) {
            echo $this->twig->render('404.twig');
        } else {
            $content = file_get_contents($contract['vzrInfo']['TiPolicies']['PolicyLink']);
            if (strlen($content) > 0) {
                $email = StorageController::get('params')['email'];
                $theme = 'Ваш полис ВЗР во вложении';
                $mess = 'Ваш полис отправлен на почту ' . $email;
                if ($contract['vzrInfo']['TiPolicies']['Status'] == 'Draft') {
                    $theme = 'Черновик Вашего полиса во вложении';
                    $mess = 'Черновик Вашего полиса отправлен на почту ' . $email;
                }
                $mail = StorageController::get('params')['email'];
                $fileName = $_SERVER['DOCUMENT_ROOT'] . '/../var/file/' . $this->contractId . '.pdf';
                file_put_contents($fileName, print_r($content, true));

                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = '10.128.0.44';
                    $mail->Port = 25;
                    $mail->CharSet = 'UTF-8';
                    $mail->SMTPSecure = false;
                    $mail->SMTPAutoTLS = false;
                    //Recipients
                    $mail->setFrom('robot@Vzr.ru', 'Vzr');
                    $mail->addAddress($email);
                    $mail->addAttachment($fileName);
                    $mail->Subject = $theme;
                    $mail->Body = $theme;
                    $mail->send();
                    $mail->ClearAddresses();
                    $mail->ClearAttachments();

                    unlink($fileName);
                    echo $this->twig->render('success-mail.twig', array('message' => $mess));
                } catch (Exception $e) {
                    echo $this->twig->render('404.twig');
                }
            }
            unset($content);
        }
    }

    private function getContract()
    {
        $controller = new VzrFormController();
        return $controller->getCleanInfo($this->contractId);
    }

    public function showSuccessPage()
    {
        echo $this->twig->render('success.twig');
    }
    public function showShortSuccessPage(){
        $message = StorageController::get('message');
        echo $this->twig->render('success-mail.twig', array('message' => $message));
    }
    public function getFilePath()
    {
        $contract = $this->getContract();
        return $contract['vzrInfo']['TiPolicies']['PolicyLink'];
    }
}
