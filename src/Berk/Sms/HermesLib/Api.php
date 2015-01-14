<?php namespace Berk\Sms\Hermeslib;

Class Api {

	var $token;
	var $error;

	private function request($postData,$url)
	{
		$ch = curl_init($url);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postData);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);                                                                        
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		$data=curl_exec($ch);
		curl_close($ch);
		return $data;
	}


	private function authenticate($config)
	{
		$postData = 'userName='.$config['userName'].'&userPass='.$config['userPass'].'&customerCode='.$config['customerCode'].'&apiKey='.$config['apiKey'].'&vendorCode='.$config['vendorCode'].'';
		$url = "https://live.iletisimmakinesi.com/api/UserGatewayWS/functions/authenticate";
		$data = $this->request($postData,$url);


		$xml=simplexml_load_string($data) or die("Hata");
		
		if($xml->STATUS->NAME == "NO_ERROR")
		{
			$this->token = $xml->CONTENT->AUTHORIZATION_WITH_TOKEN->TOKEN_NO;
			return TRUE;
			
		} else {

			$error = array("CODE" => $xml->STATUS->CODE,"DESC" => $xml->STATUS->DESC);

			$this->error = json_encode($error);
			return FALSE;
		}
	}


	public function smsSend($numara,$mesaj,$config)
	{

		if($this->authenticate($config))
		{
			$postData = 'token='.$this->token.'&phoneNumbers='.json_encode($numara).'&templateText='.$mesaj.'&originatorId='.$config['originatorId'].'&isUTF8Allowed='.$config['isUTF8Allowed'].'&validityPeriod='.$config['validityPeriod'].'&isRepeatingDestinationAllowed='.$config['isRepeatingDestinationAllowed'].'';

			$url = 'https://live.iletisimmakinesi.com/api/SMSGatewayWS/functions/sendSMS';

			$data = $this->request($postData,$url);

			$xml=simplexml_load_string($data) or die("Hata");
		
			if($xml->STATUS->NAME == "NO_ERROR")
			{
				return json_encode(array("CODE" => $xml->STATUS->CODE,"DESC" => $xml->STATUS->DESC,"ID" => $xml->CONTENT->SEND_SMS_SUCCESS->TRANSACTION->attributes()->id));

			} else {

				$error = array("CODE" => $xml->STATUS->CODE,"DESC" => $xml->STATUS->DESC);

				$this->error = json_encode($error);
				return $this->error;
			}

		} else {

			return $this->error;
		}
	}

}



?>