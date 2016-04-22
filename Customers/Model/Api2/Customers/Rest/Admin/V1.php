<?php
	/**
	* Compucom Perosna Application - Customers REST APIs Module
	*
	* NOTICE OF LICENSE
	*
	* This source file is subject to the Compucom Perosona Application End User License Agreement.
	*
	* @category    Customers
	* @package     Compucom_Customers
	* @copyright   Copyright (c) Happiest Minds
	* @license     
	*/
	
	/**
	* API2 for catalog_product (Admin)
	*
	* @category   Compucom
	* @package    Compucom_Customers
	* @author     Happiest Minds Persona Developement Team
	*/
	
	class Compucom_Customers_Model_Api2_Customers_Rest_Admin_V1 extends Compucom_Customers_Model_Api2_Customers {
		/**
		* The greatest decimal value which could be stored. Corresponds to DECIMAL (12,4) SQL type
		*/
		const MAX_DECIMAL_VALUE = 99999999.9999;
		
		
		protected function _retrieve() {
			if ($this->getRequest()->getParam('id')) {
				return $this->_retrieveCustomerID();
			} else if ($this->getRequest()->getParam('shipping_id')) {
				return $this->_retrieveShippingAddress();
			} else if ($this->getRequest()->getParam('billing_id')) {
				return $this->_retrieveBillingAddress();
			} else if ($this->getRequest()->getParam('profile_id')) {
				return $this->_retrieveCustomerProfile();
			}
		}

		protected function _update(array $data) {
			$updateProfile = $this->_updateProfile($data);
			$this->getResponse()->setBody(json_encode($updateProfile));
		}

		protected function _retrieveCustomerProfile() {
			$helper = Mage::helper('customers');
			$liferayId = $this->getRequest()->getParam('profile_id');			
        	$customerId = $helper->getCustomerId($liferayId);
			$customer = Mage::getModel('customer/customer')->load($customerId)->getData();
			return $customer;
		}

		protected function _updateProfile($data) {
			$helper = Mage::helper('customers');
			$liferayId = $this->getRequest()->getParam('id');			
        	$customerId = $helper->getCustomerId($liferayId);
			$customer = Mage::getModel('customer/customer')->load($customerId);
			//$websiteId = ($data['user_type'] == 'B2C') ? 3 : 1;
			$websiteId = 1;
			$customer->setWebsiteId($websiteId);

        	$customer->setFirstname($data['firstname']);
        	$customer->setLastname($data['lastname']);
        	$customer->setDob($data['dob']);
        	$customer->setGender($data['gender']);
        	$customer->setTelephone($data['telephone']);
        	$customer->save();

        	$responseArray['status'] = true;

        	return $responseArray;
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
