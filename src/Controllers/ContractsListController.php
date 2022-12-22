<?php

namespace Vzr\Controllers;

use Vzr\Helpers\DateHelper;
use Vzr\Helpers\FormValidation;
use Vzr\Services\ContractService;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class ContractsListController
{
    protected $params = array(
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
     * @var Environment
     */
    protected $twig;
    /**
     * @var array
     */
    protected $getParams;
    /**
     * @var false|string
     */
    protected $sign;

    public function __construct()
    {
        $loader = new FilesystemLoader($_SERVER["DOCUMENT_ROOT"] . '/../templates');
        $this->twig = new Environment($loader, array('debug' => true));
        $this->twig->addExtension(new DebugExtension());
    }

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
            $data = false;
            if ($this->checkFields() && $this->checkSign()
                || (!$this->getParams['key'] && $this->params = StorageController::get('params'))) {
                $service = new ContractService();
                $data = $service->getContractList($this->params);
            }

            if ($data['contracts']) {
                $string = http_build_query($this->params);
                StorageController::set('contractInfo', $this->sign, $data);
                echo $this->twig->render('index.twig', array('data' => $data, 'string' => $string));
            } else {
                echo $this->twig->render('404.twig');
            }
        } catch (\Exception $e) {
            echo $this->twig->render('404.twig');
        }
    }

    protected function checkFields(): bool
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

    protected function checkPostFields(): bool
    {
        foreach ($this->params as $key => $param) {
            if ($param['required'] && !$this->getParams[$key]) {
                return false;
            }
            $getParams[$key] = $_POST[$key];
        }
        $this->params = $getParams;
        return true;
    }

    protected function checkSign(): bool
    {
        $r = $this->checkSignByDate();

        if ($r === 1) {
            StorageController::setPrimary("params", $this->sign, $this->params);
            return true;
        } else {
            if ($date = DateHelper::checkDate()) {
                $r = $this->checkSignByDate($date);
                if ($r === 1) {
                    StorageController::setPrimary("params", $this->sign, $this->params);
                    return true;
                }
            }
        }
        return false;
    }

    protected function checkSignByDate($date = false, $use_local_pub_key = false)
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
        if($use_local_pub_key){
            $key = openssl_pkey_get_public(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../dsa_pub.pem"));
        } else {
            $key = openssl_pkey_get_public(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../dsa_pub123.pem"));
        }
        return openssl_verify($string, $this->sign, $key);
    }
}

