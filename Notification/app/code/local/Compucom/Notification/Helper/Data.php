<?php
class Compucom_Notification_Helper_Data extends Mage_Core_Helper_Abstract
{            

	/*
	 * Sms global settings
	 */	
	const XML_PATH_GENERAL_NOTIFY_SMS_APIKEY	    = 'general/notification_sms/username';
	const XML_PATH_GENERAL_NOTIFY_SMS_PASSWORD  = 'general/notification_sms/password';
	const XML_PATH_GENERAL_NOTIFY_SMS_ADDRESS   = 'general/notification_sms/url';
	const XML_PATH_GENERAL_NOTIFY_SMS_VERSION   = 'general/notification_sms/version';
	const XML_PATH_GENERAL_NOTIFY_SMS_SENDERID  = 'general/notification_sms/senderid';
	const XML_PATH_GENERAL_NOTIFY_SMS_SERVICENAME  = 'general/notification_sms/servicename';
	
	/*
	 * Persona webservice global settings
	 */	
	const XML_PATH_GENERAL_NOTIFIY_SERVICENAME_PORT = 'general/notification_service/port';
	const XML_PATH_GENERAL_NOTIFIY_SERVICENAME_ADDRESS = 'general/notification_service/url';
	
	/*public function sendSMS($mobileNo, $message) {
		
			if(trim($mobileNo) && trim($message))
			 {
					
				$apikey = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_APIKEY);
				$password = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_PASSWORD);
				$smsRequestTemp = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_ADDRESS);
				$version = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_VERSION);
				$senderId =  Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_SENDERID);
				$senderName = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_SERVICENAME);
	
				$uri = str_replace("{{VERSION}}",urlencode($version),
						   str_replace("{{APIKEY}}",urlencode($apikey),
						   str_replace("{{VERSION}}",urlencode($version),
						   str_replace("{{MOBILENO}}",urlencode($mobileNo),
						   str_replace("{{SENDERID}}",urlencode($senderId),
						   str_replace("{{MESSAGE}}",urlencode($message),
						   str_replace("{{SERVICENAME}}",urlencode($senderName),
						   $smsRequestTemp)))))));
			
					$ch = curl_init();
	
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	
					curl_setopt($ch,CURLOPT_URL, $uri);
					
					$output= curl_exec($ch);
					curl_close($ch);	
		
				}
				
	}*/
	
	public function sendSMS($mobileNo, $message)
	{
		
		if(trim($mobileNo) && trim($message))
			 {
					$uri = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_ADDRESS);
					
					$from = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_SENDERID);
					$to = $mobileNo;
					$body = $message;
					
					$username = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_APIKEY);
					$password = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_PASSWORD);
					
							exec("curl -X POST '".$uri."' \
					--data-urlencode 'To=".$to."'  \
					--data-urlencode 'From=".$from."'  \
					--data-urlencode 'Body=".$body."'  \
					-u ".$username.":".$password);
			 }
		
	}
	
	public function pushnotificationcall($data='')
	{
     			if(count($data)>0) {	
     			
				$notificationRequestTemp = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFIY_SERVICENAME_ADDRESS);
				$port =  Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFIY_SERVICENAME_PORT);

				$uri = str_replace("{{PORT}}",$port,$notificationRequestTemp); 
				//$uri = "http://10.16.38.70:8081/PersonaNotificationWebService/notifications/";
		
				$ch = curl_init();
				
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
				curl_setopt($ch, CURLOPT_URL, $uri); 
				curl_setopt ($ch, CURLOPT_POST, true);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
				$post = curl_exec ($ch); 
				curl_close ($ch); 
				//echo $post;		
				//die;
		}
	}
	
	
	public function sendEmail($toEmailId,$fromEmail,$var,$templateId,$storeId)
         {            
          	$sender = array('name' => $fromEmail['name'],
            'email' => $fromEmail['email']);
            $email = $toEmailId['email'];
            $name = $toEmailId['name'];                 
            $mailTemplate = Mage::getModel('core/email_template');
           //	$mailTemplate->setTemplateSubject($mailSubject)
             $mailTemplate->sendTransactional($templateId, $sender, $email, $name, $var, $storeId);
         }
         
        
    public function getBusinessDaysDiff($start, $end)
    {
        if(!$start instanceof DateTime)
            $start = date_create($start);
        if(!$end instanceof DateTime)
            $end = date_create($end);

        $totaldays = $this->getDateDiff($start, $end);
        $weeks = intval($totaldays/7);
        $remaining = $totaldays%7;
        $startWeekDay = $start->format('N');
        $remainingBusinessDays = $remaining;
        if($startWeekDay+$remaining-1 > 7)
            $remainingBusinessDays = $remainingBusinessDays - 1;
        return $remainingBusinessDays + $weeks*6;
    }

    public function getDateDiff($start, $end)
    {
        if(!$start instanceof DateTime)
            $start = date_create($start);
        if(!$end instanceof DateTime)
            $end = date_create($end);
        return date_diff($end, $start)->days;
    }

    const EMAIL_DATE_FORMAT = "d-m-Y";
    public function formatDate($date)
    {
        $dateTimestamp = Mage::getModel('core/date')->timestamp(strtotime($date));
        return date(self::EMAIL_DATE_FORMAT, $dateTimestamp);
    }

    public function formatPrice($price)
    {
    	return  Mage::getModel('directory/currency')->format(
                $price,
                array('display'=>Zend_Currency::NO_SYMBOL),
                false
                );
    }
}
