<?php namespace Berk\Sms;

class Hermes {

    protected $app;
    public    $config;
    protected $lang;
    protected $code;
    protected $success;

    public function __construct($app)
    {
            $this->app = $app;
            $this->config = $this->app['config']['sms::hermes'];
    }

               
    function smsGonder($numara, $mesaj)
    {
        $api = new HermesLib\Api();
        $sms =  $api->smsSend($numara,$mesaj,$this->config);
        return $sms;
    }

}


