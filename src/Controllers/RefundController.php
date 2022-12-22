<?php

namespace Vzr\Controllers;

use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;

class RefundController extends ContractsListController
{
    protected $params = array(
        'lastname' => array('required' => true, 'type' => 'string'),
        'name' => array('required' => true, 'type' => 'string'),
        'patronymic' => array('required' => true, 'type' => 'string'),
        'birthdate' => array('required' => true, 'type' => 'string'),
        'police-number' => array('required' => true, 'type' => 'integer'),
        'police-id' => array('required' => true, 'type' => 'integer'),
        'phone' => array('required' => true, 'type' => 'integer'),
        'email' => array('required' => true, 'type' => 'string'),
        'key' => array('required' => true, 'type' => 'string')
    );

    public function run()
    {
        try {
            $oFormValidation = FormValidation::getInstance();
            $this->getParams = $oFormValidation->xss_clean($_GET);

            if ($this->checkFields() && $this->checkSignByDate(false, true)) {

                $service = new ContractService();

                $getClaimInfo = $service->getAvansClaimInfo(
                    [
                        'PolicyId' => intval($this->params['police-id']),
                        'PolicyNumber' => intval($this->params['police-number'])
                    ]);

                if ($getClaimInfo['Success'] == "Y") {
                    //Возврат аванса доступен, продолжаем работу
                    $messages = $service->getFormMessApp([
                        'PolicyId' => intval($this->params['police-id']),
                        'FormId' => 'AC'
                    ]);

                    //Установить в сессию refund_key. Так пробрасываем ключ для pdf и mail
                    $_SESSION['refund_key'] = urldecode($this->params['key']);

                    echo $this->twig->render('refund.twig', array(
                        'message' => implode('<br>', $messages["MessageApp"][1]["Message"]),
                        'params' => $this->params,
                        'policy' => $this->params['police-id'],
                        'number' => $this->params['police-number']
                    ));

                } else {
                    //возврат аванса невозможен сообщаем о причине
                    echo $this->twig->render('franchise-404.twig', [
                        'title' => 'Заявление на возврат аванса',
                        'message' => $getClaimInfo['FaultMessage']
                    ]);
                }

            } else {
                echo $this->twig->render('404.twig');
            }
        } catch (\Exception $e) {
            echo $this->twig->render('404.twig');
        }
    }

    public function sendsms()
    {
        $num = rand(1111, 9999);
        $_SESSION['num'] = $num;
        echo file_get_contents('https://ti-8.Vzr.ru/api/?method=send_sms&phone=' . intval($_GET['phone']) . '&sms=' . $num);
    }

    public function checksms()
    {
        if (intval($_GET['num']) === $_SESSION['num']) {
            echo 'OK';
        }
    }
}

