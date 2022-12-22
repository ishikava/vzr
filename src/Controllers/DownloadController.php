<?php

namespace Vzr\Controllers;

use Vzr\Helpers\DateHelper;
use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class DownloadController
{
    /**
     * @var bool
     */
    private $contractId;
    private $params = array(
        'police-number' => array('required' => true, 'type' => 'string'),
        'name' => array('required' => true, 'type' => 'string'),
        'lastname' => array('required' => true, 'type' => 'string'),
        'patronymic' => array('type' => 'string'),
        'birthdate' => array('type' => 'string'),
        'phone' => array('required' => true, 'type' => 'integer'),
        'email' => array('required' => true, 'type' => 'integer'),
        'key' => array('required' => true, 'type' => 'string')

    );
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __construct($contractId)
    {

        $oFormValidation = FormValidation::getInstance();
        $this->getParams = $oFormValidation->xss_clean($_GET);
        $this->contractId = $oFormValidation->xss_clean(explode(".", $contractId)[0]);
    }

    /**
     * @throws \Exception
     */
    public function getFilePath()
    {
        if ($this->checkFields() && $this->checkSign()) {
            $service = new ContractService();
            $data = $service->getContractList($this->params);
            if($data['contracts'][$this->contractId]) {
                header('Content-Type: application/pdf');
                header('Content-Transfer-Encoding: Binary');
                header('Content-Disposition: attachment');
                return file_get_contents($data['contracts'][$this->contractId]['vzrInfo']['TiPolicies']['PolicyLink']);
            }
        }
        header("HTTP/1.0 404 Not Found");
        $loader = new FilesystemLoader($_SERVER["DOCUMENT_ROOT"] . '/../templates');
        $twig = new \Twig\Environment($loader, array('debug' => true));
        echo $twig->render('404.twig');
    }

    private function checkFields(): bool
    {
        foreach ($this->params as $key => $param) {
            if ($param['required'] && !$this->getParams[$key]) {
                return false;
            }
            $getParams[$key] = $_GET[$key];
        }
        $this->params = $getParams;
        return true;
    }

    private function checkSign(): bool
    {
        $r = $this->checkSignByDate();

        if ($r === 1) {
            return true;
        } else {
            if ($date = DateHelper::checkDate()) {
                $r = $this->checkSignByDate($date);
                if ($r === 1) {
                    return true;
                }
            }
        }
        return false;
    }

    private function checkSignByDate($date = false)
    {
        if (!$date) {
            $date = date('d-m-Y');
        }
        if ($this->params['key']) {
            $this->sign = base64_decode($this->params['key']);
        }
        $params = $this->params;
        unset($params['key']);
        $string = http_build_query($params) . $date;
        $key = openssl_pkey_get_public(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../dsa_pub123.pem"));
        return openssl_verify($string, $this->sign, $key);
    }

    private function prepareParams()
    {
        return array(
            'PolicyNumber' => (int)$this->params['police-number'],
            'SecondName' => $this->params['lastname'],
            'FirstName' => $this->params['name'],
            'BirthDate' => $this->params['birthdate'],
            'PhoneNumber' => preg_replace("/^[8]/", "7", preg_replace("/[^\d]/", "", $this->params['phone'])),
            'MiddleName' => $this->params['patronymic']
        );
    }
}
