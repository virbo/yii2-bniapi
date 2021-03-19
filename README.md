Component API BNI eCollection for Yii2
===========================
Component API BNI eCollection for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require virbo/yii2-bniecoll "~1.0"
```

or add

```
"virbo/yii2-kilatstobniecollrage": "~1.0"
```

to the require section of your `composer.json` file.


Usage
-----
Add this configuration to your config file

```php
'components' => [
    ...
    'bni' => [
        'class' => BniEcoll::class,
        'clientId' => 'xxx',
        'secretId' => 'xxxxxx',
        'endpoint' => BniEcoll::ENDPOINT_PRODUCTION,
    ],
    ...
],
```

#Inquiry VA
~~~php
public function actionInquiryVa()
{
    return Yii::$app->bni->inquiryVa([
        'trx_id' => 'INV/0001'
    ]);
}
~~~

#Create VA
~~~php
public function actionCreateVa()
{
    $data = [
        'client_id' => Yii::$app->bni->clientId,
        'trx_id' => 'INV/0001',
        'trx_amount' => 200000,
        'billing_type' => 'c',
        'datetime_expired' => '2021-03-19 11:17:29',
        'virtual_account' => '1234567890123456',
        'customer_name' => 'Fulan',
        'customer_email' => 'fulan@gmail.com',
        'customer_phone' => '1234567890123',
        'description' => 'Invoice for registration webinar'
    ];
    
    return Yii::$app->bni->createVa($data); //create billing without notif sms
    
    //or
    //return Yii::$app->bni->createVa($data, true); //create billing with notif sms
}
~~~

#Update VA
~~~php
public function actionUpdateVa()
{
    $data = [
        'client_id' => Yii::$app->bni->clientId,
        'trx_id' => 'INV/0001',
        'trx_amount' => 200000,
        'billing_type' => 'c',
        'datetime_expired' => '2021-03-19 11:17:29',
        'virtual_account' => '1234567890123456',
        'customer_name' => 'Fulan',
        'customer_email' => 'fulan@gmail.com',
        'customer_phone' => '1234567890123',
        'description' => 'Invoice for registration webinar'
    ];
    
    return Yii::$app->bni->updateVa($data);
}
~~~

#Using raw function
~~~php
public function actionRaw()
{
    $data = [
        'type' => 'createbilling',
        'client_id' => Yii::$app->bni->clientId,
        'trx_id' => 'INV/0001',
        'trx_amount' => 200000,
        'billing_type' => 'c',
        'datetime_expired' => '2021-03-19 11:17:29',
        'virtual_account' => '1234567890123456',
        'customer_name' => 'Fulan',
        'customer_email' => 'fulan@gmail.com',
        'customer_phone' => '1234567890123',
        'description' => 'Invoice for registration webinar'
    ];
    
    return Yii::$app->bni->sendData($data);
}
~~~