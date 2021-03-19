<?php

namespace virbo\bniecoll;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Component API BNI eCollection for Yii2
 * @author Yusuf Ayuba <yusufayuba@live.com>
 * @since  1.0
 *
 * Configuration
 * ```
 * 'components' => [
 *      ...
 *      'bni' => [
 *          'class' => BniEcoll::class,
 *          'clientId' => 'xxx',
 *          'secretId' => 'xxxxxx',
 *          'endpoint' => BniEcoll::ENDPOINT_PRODUCTION,
 *      ],
 *      ...
 * ],
 * ```
 */
class BniEcoll extends Component
{
    /**
      * endpoint production ecollection
      */
      const ENDPOINT_PRODUCTION = 'https://api.bni-ecollection.com/';

      /**
       * endpoint sandbox ecollection
       */
     const ENDPOINT_DEVELOPMENT = 'https://apibeta.bni-ecollection.com/';
 
      /**
       * var string clientId
       */
     public $clientId;
 
      /**
       * var string secretId
       */
     public $secretId;
 
      /**
       * var string prefix
       */
     public $prefix;
 
      /**
       * var string endpoint (default DEVELOPMENT)
       */
     public $endpoint = self::ENDPOINT_DEVELOPMENT;

     /**
     * Inquiry VA
     * @var array $data
     * 
     * @return array
     * 
     * How to use
     * ---
     * return Yii::$app->bni->inquiryVa([
     *      'trx_id' => $trxId
     * ]);
     */
    public function inquiryVa($data)
    {
        $type = ['type' => 'inquirybilling', 'client_id' => $this->clientId];

        $_data = ArrayHelper::merge($type, $data);
        
        return $this->sendData($_data);
    }

    /**
     * Update VA
     * @var array $data
     * 
     * @return array
     * 
     * How to use
     * ---
     * $data = [
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
     * return Yii::$app->bni->updateVa($data);
     */
    public function updateVa($data)
    {
        $type = ['type' => 'updatebilling', 'client_id' => $this->clientId];

        $_data = ArrayHelper::merge($type, $data);
        
        return $this->sendData($_data);
    }

    /**
     * Create VA
     * @var array $data
     * @var bool $notifSms
     * 
     * @return array
     * 
     *  * How to use
     * ---
     * $data = [
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
     * return Yii::$app->bni->createVa($data); //create billing without notif sms
     * 
     * or
     * 
     * return Yii::$app->bni->createVa($data, true); //create billing with notif sms
     */
    public function createVa($data, $notifSms = false)
    {
        $type = ['type' => $notifSms ? 'createbillingsms' : 'createbilling', 'client_id' => $this->clientId];

        $_data = ArrayHelper::merge($type, $data);
        
        return $this->sendData($_data);
    }

    /**
     * Send data with full parameters
     * @var $data
     * 
     * @return array
     * 
     * *  * How to use
     * ---
     * $data = [
     *      'type' => 'createbilling',
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
     * return Yii::$app->bni->sendData($data); //create billing without notif sms
     * 
     * or
     * 
     * * $data = [
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
     * return Yii::$app->bni->sendData($data); //create billing with notif sms
     */
    public function sendData($data)
    {
        $_encrypt = BniEncrypt::encrypt($data);

        $_data = Json::encode([
            'client_id' => $this->clientId,
            'prefix' => $this->prefix,
            'data' => $_encrypt,
        ]);

        $client = new Client(['baseUrl' => $this->endpoint]);
        $response = $client->createRequest()
            ->addHeaders(['content-type' => 'application/json'])
            ->setContent($_data)
            ->setMethod('post')
            ->send();

        return $response->data;
    }
}
