<?php

class Compucom_Notification_Model_System_Config_Source_Notification_Option extends Varien_Object
{
	
    public function toOptionArray()
    {
        $options = array(
 			"patmentfailure"   => Mage::helper('notification')->__('Patment Failure'),
            "orderconfirm"   => Mage::helper('notification')->__('Order Confirm'),
            "ordercomplete"   => Mage::helper('notification')->__('Order Complete')           
        );
        return $options;
    }
}
