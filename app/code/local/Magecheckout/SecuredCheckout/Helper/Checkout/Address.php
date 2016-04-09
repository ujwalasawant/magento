<?php

/**
 * Magecheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magecheckout.com license that is
 * available through the world-wide-web at this URL:
 * http://wiki.magecheckout.com/general/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 * @copyright   Copyright (c) 2015 Magecheckout (http://www.magecheckout.com/)
 * @license     http://wiki.magecheckout.com/general/license.html
 */
class Magecheckout_SecuredCheckout_Helper_Checkout_Address extends Mage_Core_Helper_Data
{

    const TEMPLATE_PATH = 'magecheckout/securedcheckout/';
    const EVENT_PREFIX = 'magecheckout_one_step_checkout_';

    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function validateAddressData($data)
    {
        $validationErrors = array();
        $requiredFields   = array(
            'country_id',
            'city',
            'postcode',
            'region_id',
        );
        foreach ($requiredFields as $requiredField) {
            if (!isset($data[$requiredField])) {
                $validationErrors[] = $this->__("Field %s is required", $requiredField);
            }
        }

        return $validationErrors;
    }

    public function getAddressTemplate()
    {
        $template = new Varien_Object(array(
            'file_path' => 'checkout/address.phtml'
        ));
        Mage::dispatchEvent(self::EVENT_PREFIX . 'get_address_template_before', array(
            'template' => $template
        ));

        return self::TEMPLATE_PATH . $template->getFilePath();
    }

    public function getBillingTemplate()
    {
        $template = new Varien_Object(array(
            'file_path' => 'checkout/address/billing.phtml'
        ));
        Mage::dispatchEvent(self::EVENT_PREFIX . 'get_billing_address_template_before', array(
            'template' => $template
        ));

        return self::TEMPLATE_PATH . $template->getFilePath();
    }

    public function getShippingTemplate()
    {
        $template = new Varien_Object(array(
            'file_path' => 'checkout/address/shipping.phtml'
        ));
        Mage::dispatchEvent(self::EVENT_PREFIX . 'get_shipping_address_template_before', array(
            'template' => $template
        ));

        return self::TEMPLATE_PATH . $template->getFilePath();

    }

    public function setDefaultShippingMethod($address)
    {
        $shippingRates = $address->setCollectShippingRates(true)->collectShippingRates()->getAllShippingRates();
        if (count($shippingRates) == 1) {
            $shippingMethod = $shippingRates[0]->getCode();
        } elseif (count($shippingRates) > 1) {
            $lastShippingMethod = Mage::helper('securedcheckout/checkout_shipping')->getLastShippingMethod();
            if ($lastShippingMethod && $address->getShippingRateByCode($lastShippingMethod)) {
                $shippingMethod = $lastShippingMethod;
            } elseif (Mage::helper('securedcheckout/config')->getDefaultShippingMethod()) {
                $shippingMethod = Mage::helper('securedcheckout/config')->getDefaultShippingMethod();
            } else {
                $shippingMethod = $shippingRates[0]->getCode();
            }
        }
        if (isset($shippingMethod)) {
            $this->getOnepage()->saveShippingMethod($shippingMethod);
        }

        return $this;
    }

    public function saveBilling($data, $customerAddressId = null)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }

        $address = $this->getQuote()->getBillingAddress();
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());
        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array(
                        'error'   => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.'),
                    );
                }
                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                $addressForm->setEntity($address);
                /*
                 *$addressErrors = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => $addressErrors);
                }
                */
            }
        } else {
            if (@class_exists('Mage_Customer_Model_Form')) {
                $addressForm->setEntity($address);
                $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
                /*
                 $addressErrors = $addressForm->validateData($addressData);
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => array_values($addressErrors));
                }
                */
                $addressForm->compactData($addressData);
                foreach ($addressForm->getAttributes() as $attribute) {
                    if (!isset($data[$attribute->getAttributeCode()])) {
                        $address->setData($attribute->getAttributeCode(), null);
                    }
                }
                $address->setCustomerAddressId(null);
                // Additional form data, not fetched by extractData (as it fetches only attributes)
                $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            } else {
                $address->addData($data);
            }
        }
        $address->implodeStreetAddress();

        if (!$this->getQuote()->isVirtual()) {
            /**
             * Billing address using otions
             */
            $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;
            switch ($usingCase) {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shipping       = $this->getQuote()->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();
                    // Billing address properties that must be always copied to shipping address
                    $requiredBillingAttributes = array('customer_address_id');
                    // don't reset original shipping data, if it was not changed by customer
                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                        if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
                            && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
                        ) {
                            $billing->unsetData($shippingKey);
                        }
                    }

                    $countryId = Mage::helper('securedcheckout/config')->getDefaultCountryId();
                    if (!$billing->getData('country_id') && $countryId) {
                        $billing->setData('country_id', $countryId);
                    }

                    $shipping
                        ->addData($billing->getData())
                        ->setSameAsBilling(1)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                    break;
            }
            $this->getQuote()->collectTotals();
            $this->getQuote()->save();
            if (!$this->getQuote()->isVirtual()) {
                //Recollect Shipping rates for shipping methods
                $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            }
        }

        return array();
    }

    public function saveShipping($data, $customerAddressId = null)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        $address = $this->getQuote()->getShippingAddress();
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());
        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array(
                        'error'   => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.'),
                    );
                }
                $address->importCustomerAddress($customerAddress);
                $addressForm->setEntity($address);
                /*
                $addressErrors = $addressForm->validateData($address->getData());
                 if ($addressErrors !== true) {
                     return array('error' => 1, 'message' => $addressErrors);
                 }
                */
            }
        } else {
            $addressForm->setEntity($address);
            // emulate request object
            $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
            /*
            $addressErrors = $addressForm->validateData($addressData);
             if ($addressErrors !== true) {
                 return array('error' => 1, 'message' => $addressErrors);
             }
            */
            $addressForm->compactData($addressData);
            // unset shipping address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), null);
                }
            }

            $address->setCustomerAddressId(null);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
        }
        $address->implodeStreetAddress();
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);

        return array();
    }


}