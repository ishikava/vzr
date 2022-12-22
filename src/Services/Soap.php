<?php


namespace Vzr\Services;


use Vzr\Helpers\IntegerHelper;
use Vzr\Helpers\Logger;

class Soap extends \SoapClient
{
    /**
     * @var string Format of time on the server.
     */
    protected $timeFormat = 'Y-m-d\TH:i:s\Z';

    /**
     * @var bool Use WS-Security.
     */
    protected $wss = false;

    /**
     * @var string
     */
    protected $wsdl;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $username;

    /**
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($wsdl, $options = array('trace' => 1, 'encoding' => 'UTF-8'))
    {
        $this->wsdl = $wsdl;
        parent::__construct($this->wsdl, $options);
    }

    /**
     * @param $data
     * @return array
     */
    public function objectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->objectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    public function call(
        $function,
        $arguments,
        $options = null,
        $inputHeaders = null,
        $outputHeaders = null,
        $transformResultToArray = true
    )
    {
        try {
            return $this->callWithoutLogException(
                $function,
                $arguments,
                $options,
                $inputHeaders,
                $outputHeaders,
                $transformResultToArray
            );
        } catch (\SoapFault $e) {
            $lastRequest = $this->__getLastRequest();
            $lastResponse = $this->__getLastResponse();
            // получим код ответа
            preg_match("/HTTP\/\d\.\d\s*\K[\d]+/", $this->__getLastResponseHeaders(), $matches);

            if ($matches[0] == 404) {
                \Vzr\Helpers\Response::show404();
                die();
            }
            // если код ответа отличный от 404 пишем в лог
            Logger::put(implode("\n", array(
                'WSDL' => $this->wsdl,
                'METHOD' => $function,
                'REQUEST' => $this->__getLastRequestHeaders() . $lastRequest,
                'RESPONSE' => $this->__getLastResponseHeaders() . $lastResponse
            )),
                        __FILE__,
                        __LINE__,
                        'SOAP');

            throw new \Exception('Hurrah!');
        }
    }


    /**
     * @param $function
     * @param $arguments
     * @param null $options
     * @param null $inputHeaders
     * @param null $outputHeaders
     * @param bool $transformResultToArray
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public function callWithoutLogException(
        $function,
        $arguments,
        $options = null,
        $inputHeaders = null,
        $outputHeaders = null,
        $transformResultToArray = true
    )
    {
        if ($this->wss) {
            $this->__setSoapHeaders($this->getSecurityHeader());
        }

        $result = $this->__soapCall($function, array($arguments), $options, $inputHeaders, $outputHeaders);

//        var_dump($function);
//        var_dump(array($arguments));
//        var_dump($options);
//        var_dump($result);

        if ($transformResultToArray) {
            return $this->objectToArray($result);
        }

        return $result;
    }

    /**
     * @param string $userName
     * @param string $password
     */
    public function setSecurity($userName, $password)
    {
        $this->username = $userName;
        $this->password = $password;
        $this->wss = true;
    }

    /**
     * @return \SoapHeader
     * @throws \Bitrix\Main\SystemException
     */
    protected function getSecurityHeader()
    {
        $nonce = IntegerHelper::getInt();
        $timestamp = gmdate($this->timeFormat);

        $digest = base64_encode(
            pack(
                'H*',
                sha1(
                    pack('H*', $nonce) .
                    pack('a*', $timestamp) .
                    pack('a*', $this->password)
                )
            )
        );

        $auth = '
        <wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.' . 'org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <wsse:UsernameToken wsu:Id="UsernameToken-14" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <wsse:Username>' . $this->username . '</wsse:Username>
                <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">' . $digest . '</wsse:Password>
                <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">' . base64_encode(pack('H*', $nonce)) . '</wsse:Nonce>
                <wsu:Created xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $timestamp . '</wsu:Created>
           </wsse:UsernameToken>
        </wsse:Security>';

        $authValues = new \SoapVar($auth, XSD_ANYXML);
        $header = new \SoapHeader(
            "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd",
            "Security",
            $authValues,
            true
        );

        return $header;
    }
}
