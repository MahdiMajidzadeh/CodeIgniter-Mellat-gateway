<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 * CodeIgniter Mellat getway library
 *
 * @author              Mahdi Majidzadeh (http://majidzadeh.ir)
 * @license             GNU Public License 2.0
 * @package             Mellat
 */

if (!class_exists('nusoap_client')) {
    require_once 'nusoap/nusoap.php';
}
class Mellat
{
    private $terminal = null;
    private $username = null;
    private $password = null;
    private $amount = null;
    private $callback = null;
    private $order = null;

    private $url_pay = 'http://interfaces.core.sw.bps.com/';
    private $url_wsdl = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';

    public function set_options($terminal = '', $username = '', $password = '', $amount = 0, $order = 0, $callback = '')
    {
        if (!empty($terminal)) {
            $this->terminal = $terminal;
        }

        if (!empty($username)) {
            $this->username = $username;
        }

        if (!empty($password)) {
            $this->password = $password;
        }

        if (!empty($amount)) {
            $this->amount = $amount;
        }

        if (!empty($order)) {
            $this->order = $order;
        }

        if (!empty($callback)) {
            $this->callback = $callback;
        }
    }

    public function call_bank()
    {
        $client = new nusoap_client($this->url_wsdl, 'wsdl');
        $terminalId = $this->terminal;
        $userName = $this->username;
        $userPassword = $this->password;
        $orderId = $this->order;
        $amount = $this->amount * 10;
        $callBackUrl = $this->callback;
        $localDate = date('Ymd');
        $localTime = date('His');
        $additionalData = '';
        $payerId = 0;

        $err = $client->getError();
        if ($err) {
            return false;
        }

        $parameters = [
            'terminalId'        => $terminalId,
            'userName'          => $userName,
            'userPassword'      => $userPassword,
            'orderId'           => $orderId,
            'amount'            => $amount,
            'localDate'         => $localDate,
            'localTime'         => $localTime,
            'additionalData'    => $additionalData,
            'callBackUrl'       => $callBackUrl,
            'payerId'           => $payerId,
        ];
        $result = $client->call('bpPayRequest', $parameters, $this->url_pay);

        if ($client->fault) {
            return false;
        } else {
            $resultStr = $result;
            $err = $client->getError();
            if ($err) {
                return false;
            } else {
                foreach ($resultStr as $value) {
                    $resultStr = $value;
                }
                $res = explode(',', $resultStr);
                $ResCode = $res[0];
                if ($ResCode == '0') {
                    return $res[1];
                } else {
                    return false;
                }
            }
        }
    }

    public function verify_payment($SaleOrderId, $SaleReferenceId)
    {
        $client = new nusoap_client($this->url_wsdl, 'wsdl');
        $orderId = $SaleOrderId;
        $verifySaleOrderId = $SaleOrderId;
        $verifySaleReferenceId = $SaleReferenceId;
        $err = $client->getError();
        if ($err) {
            return false;
        }
        $parameters = [
            'terminalId'            => $this->terminal,
            'userName'              => $this->username,
            'userPassword'          => $this->password,
            'orderId'               => $orderId,
            'saleOrderId'           => $verifySaleOrderId,
            'saleReferenceId'       => $verifySaleReferenceId,
        ];
        $result = $client->call('bpVerifyRequest', $parameters, $this->url_pay);
        if ($client->fault) {
            return false;
        } else {
            $resultStr = $result;
            $err = $client->getError();
            if ($err) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

    public function redirect_to_bank($refIdValue)
    {
        echo '<html><head></head><body></body><script language="javascript" type="text/javascript"> 
                function postRefId (refIdValue) {
                var form = document.createElement("form");
                form.setAttribute("method", "POST");
                form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");         
                form.setAttribute("target", "_self");
                var hiddenField = document.createElement("input");              
                hiddenField.setAttribute("name", "RefId");
                hiddenField.setAttribute("value", refIdValue);
                form.appendChild(hiddenField);
                document.body.appendChild(form);         
                form.submit();
                document.body.removeChild(form);
            }
            postRefId("'.$refIdValue.'");
            </script></html>';
    }

    protected function error($number)
    {
        return false;
    }

    public function get($params)
    {
        if ($params['ResCode'] == 0) {
            if ($this->verify_payment($params) == true) {
                if ($this->settle_payment($params) == true) {
                    return [
                        'status'            => 'success',
                        'ResCode'           => $params['ResCode'],
                        'RefId'             => $params['RefId'],
                        'SaleOrderId'       => $params['SaleOrderId'],
                        'SaleReferenceId'   => $params['SaleReferenceId'],
                    ];
                }
            }
        }

        return false;
    }
}
