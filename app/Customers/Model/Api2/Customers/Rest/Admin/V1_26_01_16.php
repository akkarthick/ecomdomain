<?php
	/**
	* Magento Enterprise Edition
	*
	* NOTICE OF LICENSE
	*
	* This source file is subject to the Magento Enterprise Edition End User License Agreement
	* that is bundled with this package in the file LICENSE_EE.txt.
	* It is also available through the world-wide-web at this URL:
	* http://www.magento.com/license/enterprise-edition
	* If you did not receive a copy of the license and are unable to
	* obtain it through the world-wide-web, please send an email
	* to license@magento.com so we can send you a copy immediately.
	*
	* DISCLAIMER
	*
	* Do not edit or add to this file if you wish to upgrade Magento to newer
	* versions in the future. If you wish to customize Magento for your
	* needs please refer to http://www.magento.com for more information.
	*
	* @category    Compucom
	* @package     Compucom_Catalog
	* @copyright Copyright (c) Happiest Minds
	* @license http://www.magento.com/license/enterprise-edition
	*/
	
	/**
	* API2 for catalog_product (Admin)
	*
	* @category   Compucom
	* @package    Compucom_Catalog
	* @author     Happiest Minds
	*/
	
	class Compucom_Customers_Model_Api2_Customers_Rest_Admin_V1 extends Compucom_Customers_Model_Api2_Customers {
		/**
		* The greatest decimal value which could be stored. Corresponds to DECIMAL (12,4) SQL type
		*/
		const MAX_DECIMAL_VALUE = 99999999.9999;
		
		/**
		* Retrieve Category Tree
		*
		* @return array
		*/
		
		protected function _retrieve() {
			if ($this->getRequest()->getParam('id')) {
				return $this->_retrieveCustomerID();
			} else if ($this->getRequest()->getParam('shipping_id')) {
				return $this->_retrieveShippingAddress();
			} else if ($this->getRequest()->getParam('billing_id')) {
				return $this->_retrieveBillingAddress();
			}
		}
		
		protected function _retrieveShippingAddress() {
            $liferayId = $this->getRequest()->getParam('shipping_id');

        	$helper = Mage::helper('customers');
        	$customerId = $helper->getCustomerId($liferayId);

            $model = Mage::getModel('customer/customer')->load($customerId);
            $defaultShippingAddress = $model->getDefaultShippingAddress();

            $address['customerId'] = $defaultShippingAddress->getParentId();
            $address['addressId'] = $defaultShippingAddress->getId();
            $address['firstName'] = $defaultShippingAddress->getFirstname();
            $address['lastName'] = $defaultShippingAddress->getLastname();
            $address['city'] = $defaultShippingAddress->getCity();
            $address['countryId'] = $defaultShippingAddress->getCountryId();
            $address['region'] = $defaultShippingAddress->getRegion();
            $address['postCode'] = $defaultShippingAddress->getPostcode();
            $address['telephone'] = $defaultShippingAddress->getTelephone();
            $address['street'] = $defaultShippingAddress->getStreet();

            return $address;
        }

        protected function _retrieveBillingAddress() {
            $liferayId = $this->getRequest()->getParam('billing_id');

        	$helper = Mage::helper('customers');
        	$customerId = $helper->getCustomerId($liferayId);

            $model = Mage::getModel('customer/customer')->load($customerId);
            $defaultBillingAddress = $model->getDefaultBillingAddress();

            $address['customerId'] = $defaultBillingAddress->getParentId();
            $address['addressId'] = $defaultBillingAddress->getId();
            $address['firstName'] = $defaultBillingAddress->getFirstname();
            $address['lastName'] = $defaultBillingAddress->getLastname();
            $address['city'] = $defaultBillingAddress->getCity();
            $address['countryId'] = $defaultBillingAddress->getCountryId();
            $address['region'] = $defaultBillingAddress->getRegion();
            $address['postCode'] = $defaultBillingAddress->getPostcode();
            $address['telephone'] = $defaultBillingAddress->getTelephone();
            $address['street'] = $defaultBillingAddress->getStreet();

            return $address;
        }

        protected function _retrieveCustomerID() {
        	$liferayId = $this->getRequest()->getParam('id');
        	$helper = Mage::helper('customers');
        	$customerId = $helper->getCustomerId($liferayId);
        	return $customerId;
        }
		
	}
