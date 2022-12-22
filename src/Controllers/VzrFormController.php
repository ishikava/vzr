<?php


namespace Vzr\Controllers;


use Vzr\Config\Common;
use Vzr\Helpers\Dictionaries;
use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;
use Twig\Loader\FilesystemLoader;

class VzrFormController
{
    /**
     * @var \Twig\Environment
     */
    private $twig;
    /**
     * @var bool
     */
    private $post;
    /**
     * @var mixed
     */
    private $policyId;

    /**
     * VzrFormController constructor.
     */
    public function __construct()
    {
        $this->policyId = StorageController::get('PolicyId');
        $loader = new FilesystemLoader($_SERVER["DOCUMENT_ROOT"] . '/../templates');
        $this->twig = new \Twig\Environment($loader, array('debug' => true));
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->twig->addFilter(
            new \Twig\TwigFilter('convertDateFromWS', ['\Vzr\Helpers\StringHelper', 'convertDateFromWS'])
        );
        $this->twig->addFilter(
            new \Twig\TwigFilter('convertDateTimeFromWS', ['\Vzr\Helpers\StringHelper', 'convertDateTimeFromWS'])
        );
        $oFormValidation = FormValidation::getInstance();
        $this->post = $oFormValidation->xss_clean($_POST);
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function run()
    {
        if ($this->policyId > 0
            && $contractInfo['FullVzrInfo'] = $this->getCleanInfo()) {

            $params = StorageController::get('params');
            $contractInfo['FullVzrInfo']['PhoneNumber'] = $params['phone'];
            $contractInfo['FullVzrInfo']['Email'] = $params['email'];

            $contractInfo['Programs'] = $this->getMedPrograms($contractInfo['FullVzrInfo']);
            // определим параметры для запроса сообщений
            if(StorageController::get('ModelVzrType') == Common::$shortModelVzr){
                $contracts = StorageController::get('contractInfo')["contracts"];
                $policyNumber = StorageController::get('params')['police-number'];
                $parentContract = \Vzr\Helpers\Program::getParentContract($contracts, $policyNumber);
                $policyId = $parentContract['PolicyId'];
                $formId =  1;
            } else {
                $policyId =  $this->policyId;
                $formId =  2;
            }

            $contractInfo['Message'] = $this->getMessages($policyId, $formId);

            if(StorageController::get('ModelVzrType') == Common::$shortModelVzr) {
                echo $this->twig->render('short/form.twig', array('data' => $contractInfo));
            } else {
                echo $this->twig->render('form.twig', array('data' => $contractInfo));
            }
        } else {
            echo $this->twig->render('404.twig');
        }
    }

    public function getCleanInfo($contractId = false)
    {
        $fullVzrInfo = StorageController::get('contractInfo')['contracts'];

        if (!$policyId = $contractId) {
            $policyId = $this->policyId;
        }
        foreach ($fullVzrInfo as $vzrInfo) {
            if ($policyId == $vzrInfo['PolicyId'] && !$vzrInfo['vzrInfo']['TIPolicies']['Status']) {
                return $vzrInfo;
            }
        }
        return false;
    }

    public function getMedPrograms($fullVzrInfo): array
    {
        $programs = array();

        foreach ($fullVzrInfo['vzrInfo']['MedProgInfo'] as $medProg) {
            $programs[$medProg['MedProgId']] = Dictionaries::getCurrency($medProg['CurId']);
        }
        return $programs;
    }

    public function saveContractId(): array
    {
        $result = array('success' => false);

        if ($this->post['PolicyId'] > 0) {
            StorageController::set('PolicyId', $_COOKIE['sign'], $this->post['PolicyId']);
            $result = array(
                'success' => true,
                'url' => '/policies/new/'
            );
        }
        return $result;
    }

    private function getMessages($policyId, $formId)
    {
        $params = array(
            'PolicyId' => $policyId,
            'FormId' => $formId
        );
        $service = new ContractService();
        return $service->getMessApp($params);
    }

    private function getContacts()
    {
        $contract = StorageController::get('contractInfo');
        foreach ($contract['contracts'] as $contract) {
            if ($contract['PolicyId'] == $contract['EmployeePolicyId']) {
                return array(
                    'email' => $contract['Email'],
                    'phone' => $contract['PhoneNumber']
                );
            }
        }
        return false;
    }
}
