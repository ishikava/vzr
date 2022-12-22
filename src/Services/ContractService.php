<?php

namespace Vzr\Services;

use Vzr\Config\Common;
use Vzr\Config\WebService;
use Vzr\Controllers\StorageController;
use Vzr\Helpers\Mock;
use Vzr\Helpers\Program;
use Vzr\Helpers\StringHelper;
use Vzr\Services\Soap;
use Twig\Loader\FilesystemLoader;

class ContractService
{

    /**
     * @var Soap
     */
    private $client;

    public function __construct()
    {
        $wsConfig = WebService::getEnv()['contractInfo'];
        $this->client = new Soap(
            $wsConfig['host']
            //, array('exceptions' => FALSE)//Эта опция позволяет видеть прямой ответ SOAP сервера
        );
        $this->client->setSecurity($wsConfig['user'], $wsConfig['password']);
    }

    /**
     * @return array|\array[][][]|false
     */
    public function getContractList($params)
    {
        $vzrInfo = false;
        if($contractInfo = $this->getContractInfo($params)) {
            if ($contractInfo['RegisterOperationInfo']['PolicyId']) {
                $contractInfo['RegisterOperationInfo'] = array( //если вернулся только один полис - пихаем его в массив для унификации
                    $contractInfo['RegisterOperationInfo']
                );
            }
            foreach ($contractInfo['RegisterOperationInfo'] as $info) {
                $newContracts[$info['PolicyId']] = $info;
                $policies[] = $info['PolicyId'];
                if ($info['PolicyId'] == $info['EmployeePolicyId']) {
                    $messPolicyId = $info['EmployeePolicyId'];
                    $messFormType = $info['ModelVzr'];
                }
            }
            $contractInfo = $newContracts;

            if ($policies) {
                /**
                 * Определим схему оформления
                 */
                $parentContract = Program::getParentContract($contractInfo, (int)$params['police-number']);
                $modelVzrType = $parentContract['ModelVzr'];
                if ($modelVzrType == Common::$shortModelVzr) {
                    StorageController::set('ModelVzrType', $_COOKIE['sign'], 1);
                    $vzrInfo = $this->getVzrShortContract(
                         $policies
                    );
                    /**
                     * Логика для короткой схемы
                     */

                    if (!$vzrInfo['GetShortVzrInfoData']) {
                        throw new \Exception('Не удалось получить полисы');
                    }
                    if ($vzrInfo['GetShortVzrInfoData']['PolicyId']) {
                        $vzrInfo['GetShortVzrInfoData'] = array( //если вернулся только один полис - пихаем его в массив для унификации
                            $vzrInfo['GetShortVzrInfoData']
                        );
                    }

                    $messParam = array(
                        'PolicyId' => $messPolicyId,
                        'FormId' => $messFormType
                    );

                    $message = $this->getMessApp($messParam);

                    foreach ($vzrInfo['GetShortVzrInfoData'] as $info) {

                            $info['TiPolicies'] = $info['TiPolicy'];
                            unset($info['TiPolicy']);

                        foreach($info['TiPolicies'] as $id=>$tiPolicy) {
                            if($tiPolicy['Status'] && $tiPolicy['Status'] != 'Cancelled') {
                                $info['TiPolicies'] = $tiPolicy;
                                break;
                            }
                        }

                        $contractInfo[$info['PolicyId']]['vzrInfo'] = $info;
                    }

                } else {
                    /**
                     * Логика для полной схемы
                     */
                    $vzrInfo = $this->getVzrFullContract(
                        $policies
                    );
                    if (!$vzrInfo['GetFullVzrInfoData']) {
                        throw new \Exception('Не удалось получить полисы');
                    }
                    if ($vzrInfo['GetFullVzrInfoData']['PolicyId']) {
                        $vzrInfo['GetFullVzrInfoData'] = array( //если вернулся только один полис - пихаем его в массив для унификации
                            $vzrInfo['GetFullVzrInfoData']
                        );
                    }

                    $messParam = array(
                        'PolicyId' => $messPolicyId,
                        'FormId' => $messFormType
                    );
                    $message = $this->getMessApp($messParam);

                    foreach ($vzrInfo['GetFullVzrInfoData'] as $info) {
                        foreach($info['TiPolicies'] as $id=>$tiPolicy) {
                            if($tiPolicy['Status'] && $tiPolicy['Status'] != 'Cancelled') {
                                $info['TiPolicies'] = $tiPolicy;
                                break;
                            }
                        }
                        if ($info['MedProgInfo']['MedProgId']) { //если вернулась одна программа - суем ее в массив
                            $info['MedProgInfo'] = array(
                                $info['MedProgInfo']
                            );
                        }
                        $contractInfo[$info['PolicyId']]['vzrInfo'] = $info;
                    }
                    foreach ($contractInfo as $id => $contract) {
                        if (!$contract['vzrInfo']['MedProgInfo']) {
                            unset($contractInfo[$id]);
                        }
                    }
                }
            }

            return array(
                'contracts' => $contractInfo,
                'message' => $message
            );
        }
    }

    public function getContractInfo($params)
    {
        $clientParams = array(
            'PolicyNumber' => (int)$params['police-number'],
            'SecondName' => $params['lastname'],
            'FirstName' => $params['name'],
            'BirthDate' => $params['birthdate'],
            'PhoneNumber' => preg_replace("/^[8]/", "7", preg_replace("/[^\d]/", "", $params['phone'])),
            'MiddleName' => $params['patronymic']
        );

        //return Mock::registerOperation();
        return $this->client->call('RegisterOperation', $clientParams)['RegisterOperationData'];
    }

    private function getVzrFullContract($employeeContract): array
    {
        $clientParams = array('PolicyId' => $employeeContract);

        //return Mock::getFullVzrInfo();
        return $this->client->call('getFullVzrInfoOperation', $clientParams);
    }

    private function getVzrShortContract($employeeContract): array
    {
        $clientParams = array('PolicyId' => $employeeContract);

        return $this->client->call('getShortVzrInfoOperation', $clientParams);
    }

    public function getMessApp($messParam): array
    {
        //return Mock::getMessApp();
        return $this->client->call('getMessAppOperation', $messParam);
    }

    public function createPolicy(array $params)
    {
        //return Mock::createFullVzrPolicy();
        return $this->client->call('createFullVzrPolicyOperation', $params);
    }

    public function createShortVzrPolicy(array $params)
    {
        return $this->client->call('CreateShortVzrPolicy', $params);
    }


    public function deleteOrAcceptPolicy(array $params)
    {
        return $this->client->call('ChangeFullVzrPolicyStatus', $params);
    }

    public function getRegisterOperation(array $params)
    {
    }

    public function getFranchiseCertificateList($listParam): array
    {
        return $this->client->call('GetFranchiseCertificateList', $listParam);
    }

    public function getFranchiseCertificateDoc($listParam): array
    {
        return $this->client->call('GetFranchiseCertificateDoc', $listParam);
    }

    public function getAvansClaimInfo($params): array
    {
        return $this->client->call('GetAvansClaimInfo', $params);
    }

    public function getFormMessApp($messParam): array
    {
        return $this->client->call('GetFormMessApp', $messParam);
    }

    public function updateClaimInsured($params): array
    {
        return $this->client->call('UpdateClaimInsured', $params);
    }
}
