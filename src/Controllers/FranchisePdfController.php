<?php

namespace Vzr\Controllers;

use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;

class FranchisePdfController extends FranchiseController
{
    protected $params = array(
        'police-id' => array('required' => true, 'type' => 'integer'),
        'subj-id' => array('required' => true, 'type' => 'integer'),
        'phone' => array('required' => true, 'type' => 'integer'),
        'sum' => array('required' => true, 'type' => 'string'),
        'key' => array('required' => true, 'type' => 'string')
    );

    public function pdf()
    {
        try {
            $oFormValidation = FormValidation::getInstance();
            $this->getParams = $oFormValidation->xss_clean($_GET);
            $check = $this->checkFields();
            if ($check && $_SESSION['franchise_key'] == urldecode($this->params['key'])) {
                $service = new ContractService();
                $doc = $service->getFranchiseCertificateDoc([
                    'PolicyId' => intval($this->params['police-id']),
                    'ChildSubjId' => intval($this->params['subj-id']),
                    'Login' => '+7' . substr(intval($this->params['phone']), 1, strlen($this->params['phone']) -1),//Заменить первую цифру телефона на +7
                    'FnsTotalSum' => $this->params['sum']
                ]);
                header('Content-Type: application/pdf');
                header('Content-Transfer-Encoding: Binary');
                header('Content-Disposition: attachment');
                echo $doc['docPDF'];
            } else {
                echo $this->twig->render('404.twig');
            }
        } catch
        (\Exception $e) {
            echo $this->twig->render('404.twig');
        }
    }

}

