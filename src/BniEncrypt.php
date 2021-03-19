<?php

namespace virbo\bniecoll;

use yii\helpers\Json;

/**
 * BNI Encrypt for BNI Ecollection
 * @author Yusuf Ayuba <yusufayuba@live.com>
 * @since  1.0
 */
class BniEncrypt
{
    /**
     * parameters default from BinEnc
     */
    const TIME_DIFF_LIMIT = 480;

    /**
     * encrypt data
     * @var data
     * 
     * @return string
     * 
     * How to use
     * ---
     * $data = [
     *      'type' => 'createbillingsms',
     *      'client_id' => Yii::$app->bni->clientId,
     *      'trx_id' => $trxId,
     *      'trx_amount' => $trxAmount,
     *      'billing_type' => $billingType,
     *      'datetime_expired' => $datetimeExpired,
     *      'virtual_account' => $virtualAccount,
     *      'customer_name' => $customerName,
     *      'customer_email' => $customerEmail,
     *      'customer_phone' => $customerPhone,
     *      'description' => $description
     * ];
     * 
     * return Yii->$app->bni->encrypt($data);
     * 
     * */
    public function encrypt($data = [])
    {
        return $this->doubleEncrypt(strrev(time()).'.'.Json::encode($data));
    }

    /**
     * decrypt data
     * @var data
     * 
     * @return array
     * 
     *  * 
     * How to use
     * ---
     * 
     * return Yii->$app->bni->decrypt($data);
     */
    public function decrypt($hasedString)
    {
        $parsedString = $this->doubleDecrypt($hasedString);
        list($timestamp, $data) = array_pad(explode('.',$parsedString,2),2,null);

        //return self::tsDiff(strrev($timestamp)) ? Json::decode($data,true) : null;
        return Json::decode($data);
    }

    protected static function tsDiff($ts)
    {
        return abs($ts - time()) <= self::TIME_DIFF_LIMIT;
    }

    protected function doubleEncrypt($string)
    {
        $result = self::enc($string, $this->clientId);
        $result = self::enc($result, $this->secretId);

        return strtr(rtrim(base64_encode($result), '='), '+/', '-_');
    }

    protected static function enc($string, $key)
    {
        $result = '';
        $lengthString = strlen($string);
        $lengthKey = strlen($key);

        for ($i=0; $i<$lengthString; $i++) {
            $char = substr($string, $i, 1);
            $keyChar = substr($key, ($i % $lengthKey) - 1, 1);
            $char = chr((ord($char) + ord($keyChar)) % 128);
            $result .= $char;
        }

        return $result;
    }

    protected function doubleDecrypt($string)
    {
        $result = base64_decode(strtr(str_pad($string, ceil(strlen($string)/4)*4,'=',STR_PAD_RIGHT),'-_','+/'));
        $result = self::dec($result, $this->clientId);
        $result = self::dec($result, $this->secretId);

        return $result;
    }

    protected static function dec($string, $key)
    {
        $result = '';
        $lengthString = strlen($string);
        $lengthKey = strlen($key);

        for ($i=0; $i<$lengthString; $i++) {
            $char = substr($string, $i, 1);
            $keyChar = substr($key, ($i % $lengthKey) - 1, 1);
            $char = chr(((ord($char) - ord($keyChar)) + 256) % 128);
            $result .= $char;
        }

        return $result;
    }
}
