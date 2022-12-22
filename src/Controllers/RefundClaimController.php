<?php

namespace Vzr\Controllers;

use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;

class RefundClaimController extends ContractsListController
{
    protected $params = array(
        'email' => array('required' => true, 'type' => 'string'),
        'original-email' => array('required' => true, 'type' => 'string'),
        'police-number' => array('required' => true, 'type' => 'string'),
        'phone' => array('required' => true, 'type' => 'string'),
        'pass-ser' => array('required' => true, 'type' => 'integer'),
        'pass-no' => array('required' => true, 'type' => 'integer'),
        'pass-issue-place' => array('required' => true, 'type' => 'integer'),
        'pass-issue-date' => array('required' => true, 'type' => 'string'),
        'bank-name' => array('required' => true, 'type' => 'string'),
        'bank-corr-account' => array('required' => true, 'type' => 'string'),
        'bank-bik' => array('required' => true, 'type' => 'string'),
        'bank-account' => array('required' => true, 'type' => 'string'),
        'fio' => array('required' => true, 'type' => 'string'),
        'operation' => array('required' => true, 'type' => 'string'),
        'key' => array('required' => true, 'type' => 'string')
    );

    public function run()
    {

        try {
            $oFormValidation = FormValidation::getInstance();
            $this->getParams = $oFormValidation->xss_clean($_POST);
            $check = $this->checkPostFields();
            if ($check && $_SESSION['refund_key'] == urldecode($this->params['key'])) {
                $service = new ContractService();

                $object = new \stdClass();

                $object->MobilePhone = '+7' . substr(intval($this->params['phone']), 1, strlen($this->params['phone']) -1);//Заменить первую цифру телефона на +7;
                $object->Email = $this->params['original-email'];
                $object->FIO = $this->params['fio'];
                $object->PassNo = $this->params['pass-no'];
                $object->PassSer = $this->params['pass-ser'];
                $object->PassIssueDate = date('Y-m-d', strtotime($this->params['pass-issue-date']));
                $object->PassIssuePlace = $this->params['pass-issue-place'];
                $object->BankName = $this->params['bank-name'];
                $object->BankCorrAccount = $this->params['bank-corr-account'];
                $object->BankBik = $this->params['bank-bik'];
                $object->BankAccount = $this->params['bank-account'];
                $object->ClaimReason = 'Возврат неиспользованного депозита';

                switch ($this->params['operation']) {
                    case 'draft':
                        $claimids = [];
                        $accept = false;
                        break;
                    case 'update':
                        $claimids = $_SESSION['claimids'];
                        $accept = false;
                        break;
                    case 'accept':
                        $claimids = $_SESSION['claimids'];
                        $accept = true;
                        break;
                }

                $response = $service->updateClaimInsured(
                    [
                        'UpdateClaimInsuredRequest' => '',
                        'PolicyNumber' => intval($this->params['police-number']),
                        'ClaimType' => 1,
                        'UpdateClaimInsuredInfo' => $object,
                        'ClaimIds' => $claimids,
                        'Accept' => $accept
                    ]
                );

                if ($response['Success'] == 'Y') {

                    $_SESSION['claimids'] = $response['ClaimIds'];

                    $filenames = [];

                    foreach ($response['ClaimPrintForms'] as $key => $item) {
                        $filenames[] = '<div class="re-item"><span>' . ($key + 1) . '.</span> ' . $item['Filename'] . '</div>';
                    }

                    echo '<div class="formresponse"><div class="rf-item-container re-container">Черновик заявлений:</div>
                <div class="re-items">' . implode(' ', $filenames) . '</div>
                <div class="re-email js-popup" data-popup-widget-target="js-popup"><img src="/images/email.svg" alt="отправить на почту">
                    <div class="re-email-text">Отправить на почту</div>
                </div></div>';

                } else {
                    //возврат аванса невозможен сообщаем о причине
                    echo $this->twig->render('franchise-404.twig', [
                        'title' => 'Заявление на возврат аванса',
                        'message' => $response['FaultMessage']
                    ]);
                }


            } else {
                echo $this->twig->render('404.twig');
            }
        } catch (\Exception $e) {
            echo $this->twig->render('404.twig');
        }
    }

}

