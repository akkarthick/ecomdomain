<?php

class Compucom_Notification_Model_Customer extends Mage_Customer_Model_Customer
{
  const XML_PATH_GENERAL_NOTIFICATION_SEND_CUSTOMER_EMAIL_FLAG = 'general/notification/send_customer_welcome_email';

  public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
   	
    	if (Mage::getStoreConfig(self::XML_PATH_GENERAL_NOTIFICATION_SEND_CUSTOMER_EMAIL_FLAG)== 0) {
            return $this;
        }
    	
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('customer' => $this, 'back_url' => $backUrl), $storeId);

        return $this;
    }
	
	
}