<?php

namespace Vzr\Controllers;

use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class FranchiseController extends ContractsListController
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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function run()
    {
        try {
            $oFormValidation = FormValidation::getInstance();
            $this->getParams = $oFormValidation->xss_clean($_GET);
            if ($this->checkFields() && $this->checkSignByDate(false, false)) {
                $service = new ContractService();
                $list = $service->getFranchiseCertificateList([
                    'Login' => intval($this->params['phone']),
                    'PolicyId' => intval($this->params['police-id']),
                    'PolicyNumber' => intval($this->params['police-number'])
                ]);
                $message = $service->getMessApp([
                    'PolicyId' => intval($this->params['police-id']),
                    'FormId' => 3
                ]);
                if (isset($list['MessFrancCertIsNull'])) {
                    echo $this->twig->render('franchise-404.twig', array('message' => $list['MessFrancCertIsNull']));
                } else {
                    $prepared_list = [];
                    //в списке только один полис
                    if (isset($list['FranchiseCertificateList']['SubjId'])) {
                        $prepared_list[] = $list['FranchiseCertificateList'];
                    } else {
                        //в списке несколько полисов
                        $prepared_list = $list['FranchiseCertificateList'];
                    }

                    //исправление мелких деталей в списке справок
                    $prepared_list = $this->listNormalization($prepared_list);

                    //Установить в сессию franchise_key. Так пробрасываем ключ для pdf и mail
                    $_SESSION['franchise_key'] = urldecode($this->params['key']);

                    //Установить в сессию back link
                    $backlink = [];
                    foreach ($this->params as $key => $value) {
                        $backlink[] = $key . '=' . $value;
                    }
                    $_SESSION['backlink'] = implode('&', $backlink);

                    echo $this->twig->render('franchise.twig', array(
                        'list' => $prepared_list,
                        'message' => $message['MessAppList']['Mess01'],
                        'email' => $this->params['email'],
                        'policy' => $this->params['police-id'],
                        'number' => $this->params['police-number'],
                        'phone' => $this->params['phone'],
                        'key' => $this->params['key'],
                    ));
                }
            } else {
                echo $this->twig->render('404.twig');
            }
        } catch (\Exception $e) {
            echo $this->twig->render('404.twig');
        }
    }

    private function listNormalization($list): array
    {
        foreach ($list as &$item) {
            $item['AgreementDateFrom'] = date('d-m-Y', strtotime($item['AgreementDateFrom']));
            $item['AgreementDateTo'] = date('d-m-Y', strtotime($item['AgreementDateTo']));
        }
        unset($item);
        return $list;
    }

}

