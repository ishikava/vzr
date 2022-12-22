<?php

namespace Vzr\Controllers;

use Vzr\Config\Common;
use Vzr\Helpers\FormValidation;
use Vzr\Helpers\Logger;
use Vzr\Services\ContractService;

class CreateVzrController
{

    public function __construct()
    {
        $oFormValidation = FormValidation::getInstance();
        $this->post = $oFormValidation->xss_clean($_POST);
    }

    public function createFullVzrPolicy()
    {
        $result = array(
            'success' => false
        );
        try {
            $service = new ContractService();

            if (StorageController::get('ModelVzrType') == Common::$shortModelVzr) {
                $params = $this->makeShortParamArray();

                $data = $service->createShortVzrPolicy($params);
                if ($data["Successes"] == '0' ) {
                    StorageController::set('message', $_COOKIE['sign'], $data["Message"]);
                    $result = array(
                        'success' => true,
                        'url' => '/policies-short/success'
                    );
                } else {
                    if ($data["Successes"] == '101') {
                        $result['message'] = $data["Message"];
                    }
                }
                if($data["Successes"]!='0') {
                    Logger::put(implode("\n", array(
                        'METHOD' => 'createShortVzrPolicy',
                        'REQUEST' => serialize($params),
                        'RESPONSE' => serialize($data)
                    )),
                        __FILE__,
                        __LINE__,
                        'SOAP');
                }
            } else {
                $params = $this->makeParamArray();
                $data = $service->createPolicy($params);
                if ($data["Success"] == '0' && $params = StorageController::get('params')) {
                    $newContract = $service->getContractList($params);
                    StorageController::set('contractInfo', $_COOKIE["sign"], $newContract);
                    $result = array(
                        'success' => true,
                        'url' => '/policies/draft/' . StorageController::get('PolicyId')
                    );
                } else {
                    if ($data["Success"] == '-100') {
                        $result['message'] = $data["ErrorMessage"];
                    }
                }
                if($data["Success"]!='0') {
                    Logger::put(implode("\n", array(
                        'METHOD' => 'createFullVzrPolicyOperation',
                        'REQUEST' => serialize($params),
                        'RESPONSE' => serialize($data)
                    )),
                        __FILE__,
                        __LINE__,
                        'SOAP');

                }
            }
        } catch (\Exception $e) {
            return $result;
        }
        return $result;
    }

    private function makeParamArray()
    {
        $formcontroller = new VzrFormController();
        $contractInfo = $formcontroller->getCleanInfo();

        $curMedProg = $this->getChosenMedProg($contractInfo['vzrInfo']);
        return array(
            'EmployeePolicyId' => $contractInfo['EmployeePolicyId'],
            'PolicyId' => StorageController::get('PolicyId'),
            'LatinFIO' => strtoupper($this->post["lastname"] . " " . $this->post["firstname"]),
            'BirthDate' => explode("T", $contractInfo['Birthday'])[0],
            'Email' => $this->post["email"],
            'Phone' => preg_replace("/[^\d]/", "", $this->post["tel"]),
            'Territory' => $contractInfo['vzrInfo']["Territory"],
            'MedProgId' => $curMedProg["MedProgId"],
            'CostRur' => $curMedProg["CostRur"],
            'CostVal' => $curMedProg["CostVal"],
            'CurId' => $curMedProg["CurId"]
        );
    }

    private function makeShortParamArray()
    {
        $formcontroller = new VzrFormController();
        $contractInfo = $formcontroller->getCleanInfo();

        return array(
            'EmployeePolicyId' => $contractInfo['EmployeePolicyId'],
            'PolicyId' => StorageController::get('PolicyId'),
            'LatinFIO' => strtoupper($this->post["lastname"] . " " . $this->post["firstname"]),
            'BirthDate' => explode("T", $contractInfo['Birthday'])[0],
            'Email' => $this->post["email"],
            'Phone' => preg_replace("/[^\d]/", "", $this->post["tel"]),
            'Territory' =>   $this->post["territory"],
            'ExpirationDate' => $contractInfo['vzrInfo']['ExpirationDate'],
        );
    }

    private function getChosenMedProg($contractInfo)
    {
        foreach ($contractInfo['MedProgInfo'] as $medProg) {
            if ($medProg["MedProgId"] == $this->post["program"]) {
                return $medProg;
            }
        }
        return false;
    }

    public function deleteOrAcceptVzrPolicy($status)
    {
        $result = array('success' => false);
        $url = '/policies/';
        if($status == 2) {
            $url = '/policies/success';
        }
        $formcontroller = new VzrFormController();
        if ($contractInfo = $formcontroller->getCleanInfo($this->post['policyId'])) {
            try {
                if ($contractInfo['vzrInfo']['TiPolicies']['Status'] == 'Draft') {
                    $params = array(
                        'EmployeePolicyId' => $contractInfo['EmployeePolicyId'],
                        'newStatus' => (int)$status,
                        'policyId' => $this->post['policyId'],
                        'TiPolicies' => $contractInfo['vzrInfo']['TiPolicies']
                    );
                    $service = new ContractService();
                    $data = $service->deleteOrAcceptPolicy($params);
                    if ($data['successes'] == 0) {
                        $result = array('success' => true, 'url' => $url);
                    }
                }
            } catch (\Exception $e) {
                return $result;
            }
        }
        return $result;
    }
}
