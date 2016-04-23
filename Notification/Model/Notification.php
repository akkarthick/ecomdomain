<?php
class Compucom_Notification_Model_Notification extends Mage_Core_Model_Abstract
{
	const ORDER_STATUS_PENDING = 'pending';
	const ORDER_STATUS_PENDING_PAYMENT = 'pending_payment';
	const ORDER_STATUS_PAYMENT_FAILURE = 'payment_failure';
	const ORDER_STATUS_PROCESSING = 'processing';
	const ORDER_STATUS_COMPLETE = 'complete';
	const ORDER_STATUS_CANCELLED = 'canceled';
	const XML_PATH_EMAIL_IDENTITY_ORDER_COMPLETED   = 'sales_email/order/identity';
	const ORDER_PAYMENT_FALIURE_EMAIL_TEMPLATE = 'sales_email_template_compucom_payment_failure';
	
	const XML_TRANS_EMAIL_NAME = 'trans_email/ident_sales/name';
	const XML_TRANS_EMAIL_ID = 'trans_email/ident_sales/email';

	/*
	 * Notification Variables
	 */
	const XML_PATH_GENERAL_NOTIFICATION_PAYMENTFAILURE_TITLE = 'general/notification_payment_failure/paymentfailure_title';
	const XML_PATH_GENERAL_NOTIFICATION_PAYMENTFAILURE_DESCRIPTION = 'general/notification_payment_failure/paymentfailure_description';
	const XML_PATH_GENERAL_NOTIFICATION_PAYMENTFAILURE_SMS_MESSAGE = 'general/notification_payment_failure/paymentfailure_sms_message';
	const XML_PATH_GENERAL_NOTIFICATION_ORDERCOMFIRM_TITLE= 'general/notification_order_confirm/orderconfirm_title';
	const XML_PATH_GENERAL_NOTIFICATION_ORDERCOMFIRM_DESCRIPTION= 'general/notification_order_confirm/orderconfirm_description';
	const XML_PATH_GENERAL_NOTIFICATION_ORDERCOMFIRM_SMS_MESSAGE= 'general/notification_order_confirm/orderconfirm_sms_message';
	const XML_PATH_GENERAL_NOTIFICATION_ORDERCOMPLETE_TITLE= 'general/notification_order_complete/ordercomplete_title';
	const XML_PATH_GENERAL_NOTIFICATION_ORDERCOMPLETE_DESCRIPTION = 'general/notification_order_complete/ordercomplete_description';
	const XML_PATH_GENERAL_NOTIFICATION_ORDERCOMPLETE_SMS_MESSAGE = 'general/notification_order_complete/ordercomplete_sms_message';

	const XML_PATH_GENERAL_NOTIFY_SMS_ENABLED	    = 'general/notification/sms_enabled';
	const XML_PATH_GENERAL_NOTIFY_PUSHNOTIFICATION_ENABLED  = 'general/notification/pushnotification';
	
	const XML_PATH_GENERAL_COMPUCOM_CRON_SCHEDULAR_ENABLED  = 'general/notification/compucom_cronjobs_status';
	
	protected function _construct()
	{
		$this->_init('notification/notification');
	}
	
	public function pushNotification($templateData)
	{
		 $notifyEnabled = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_PUSHNOTIFICATION_ENABLED);	

	 	if($notifyEnabled=='1') 
		{
			
			$data = array(
					  "title" => $templateData['title'],
					  "userId"  =>  $templateData['userId'],
					  "src"  =>  "eCommerce",
					  "category"  =>  "orderUpdate",
					  "notificationType"  =>  0,
					  "priority"  =>  0,
					  "notificationDate"  =>  date("Y-m-d\TH:i:s"),
					   "content"=>array(array("name"=>"description",
									"value"=>$templateData['description']))				
				);				
			
			$pushcall = Mage::helper('notification')->pushnotificationcall($data);
			
			return;
		}

	}

	public function orderSMSPaymentFailure($order,$smsTemplate)
	{
		$customerName = $order->getCustomerName();
		$smsMessageTemplate =  $this->getStoreConfigData($smsTemplate);	
		$smsMessagecontent =  str_replace("{{CUSTOMER_NAME}}",$customerName,$smsMessageTemplate);
		$this->sendSMS($mobileNumber,$smsMessagecontent);			
	}
	
	public function orderPlacementSendSMS($order,$smsTemplate) {

			$orderNumber = $order->getIncrement_id();
			$customerName = $order->getCustomerName();
			$mobileNumber=$order->getShippingAddress()->getTelephone();			
			$smsMessageTemplate =  $this->getStoreConfigData($smsTemplate);			
			$smsMessagecontent = str_replace("{{CUSTOMER_NAME}}",$customerName,
								str_replace("{{ORDER_NO}}",$orderNumber,$smsMessageTemplate));		
			$this->sendSMS($mobileNumber,$smsMessagecontent);	

	}

	public function orderEmailPaymentFailure($order,$emailTemplateId) {
		
		$storeId =   $order->getStoreId();   
		$sender =array('name'=>Mage::getStoreConfig(self::XML_TRANS_EMAIL_NAME), 'email'=>Mage::getStoreConfig(self::XML_TRANS_EMAIL_ID)) ;		
		$customerName = $order->getCustomerName();		
        $customerEmail = $order->getCustomerEmail();
		$to =array('name'=>$customerName,'email'=>$customerEmail);	
		$vars['order']=$order;		
		Mage::helper('notification')->sendEmail($to,$sender,$vars,$emailTemplateId,$storeId);
	}

	public function orderCompletedSendSMS($order,$smsTemplate) {
		
			$orderNumber = $order->getIncrement_id();
			$mobileNumber=$order->getShippingAddress()->getTelephone();
			$smsMessageTemplate =  $this->getStoreConfigData($smsTemplate);
			$smsMessagecontent = str_replace("{{CUSTOMER_NAME}}",$customerName,
								str_replace("{{ORDER_NO}}",$orderNumber,$smsMessageTemplate));	
			$this->sendSMS($mobileNumber,$smsMessagecontent);
	

	}

	public function getStoreConfigData($constname) {
		$constvalue = Mage::getStoreConfig($constname);
		return $constvalue;
	}

	public function sendSMS($mobileNumber,$smsMessagecontent)
	{
		$notifyEnabled = Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFY_SMS_ENABLED);	
		if($notifyEnabled=='1') 
		{
				$send =  Mage::helper('notification')->sendSMS($mobileNumber,$smsMessagecontent);
				return;
		}
	}
  
	public function getNotificationFrontendData($orderData,$status)
	{
		$orerNumber = $orderData->getIncrement_id();
		if(trim($status)== self::ORDER_STATUS_PAYMENT_FAILURE) {
			$title = trim(Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFICATION_PAYMENTFAILURE_TITLE));
			
			$templateData = array('title'=>$title,
									   'userId'=>trim($orderData->getCustomer_email()),
										'description' =>trim(Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFICATION_PAYMENTFAILURE_DESCRIPTION))			
								);

		}
		elseif(trim($status)==self::ORDER_STATUS_PROCESSING) {
			$titleTemp = trim(Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFICATION_ORDERCOMFIRM_TITLE));
			$title  = str_replace("{{ORDER_NO}}",$orerNumber,$titleTemp);
			$templateData = array('title'=>$title,
									   'userId'=>trim($orderData->getCustomer_email()),
										'description' =>trim(Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFICATION_ORDERCOMFIRM_DESCRIPTION))		
								);
		}
		elseif(trim($status)==self::ORDER_STATUS_COMPLETE) {
			
			$titleTemp = trim(Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFICATION_ORDERCOMPLETE_TITLE));
			$title  = str_replace("{{ORDER_NO}}",$orerNumber,$titleTemp);
			
			$templateData = array('title'=>$title,
									   'userId'=>trim($orderData->getCustomer_email()),
										'description' =>trim(Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFICATION_ORDERCOMPLETE_DESCRIPTION))			
								);
		}
		
		return $templateData;
	}

	public function notificationContentData($orderData)
	{
		$status = $orderData->getStatus();
		
		if(trim($status)==self::ORDER_STATUS_PAYMENT_FAILURE) {
				
				$templateData = $this->getNotificationFrontendData($orderData,$status);
				
				$this->pushNotification($templateData);
				
				$this->orderSMSPaymentFailure($orderData,self::XML_PATH_GENERAL_NOTIFICATION_PAYMENTFAILURE_SMS_MESSAGE);
				
				$this->orderEmailPaymentFailure($orderData,self::ORDER_PAYMENT_FALIURE_EMAIL_TEMPLATE);
		}

		elseif(trim($status)==self::ORDER_STATUS_PROCESSING) {
			
			$templateData = $this->getNotificationFrontendData($orderData,$status);
			
			$this->pushNotification($templateData);	
			
			// Sending SMS		
			$this->orderPlacementSendSMS($orderData,self::XML_PATH_GENERAL_NOTIFICATION_ORDERCOMFIRM_SMS_MESSAGE);
			
		}

		elseif(trim($status)==self::ORDER_STATUS_COMPLETE) {

			$templateData = $this->getNotificationFrontendData($orderData,$status);
			
			$this->pushNotification($templateData);
			
			$this->orderCompletedSendSMS($orderData,self::XML_PATH_GENERAL_NOTIFICATION_ORDERCOMPLETE_SMS_MESSAGE);
			
				
		}

	}

	
}
