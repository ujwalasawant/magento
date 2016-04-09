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
class Magecheckout_SecuredCheckout_IndexController extends Mage_Checkout_Controller_Action
{
    /*
     * Reference from app/code/core/Mage/Checkout/controllers/OnepageController.php:58
    */

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();

        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }

        if (!$this->_showForUnregisteredUsers()) {
			Mage::getSingleton('customer/session')->setAfterAuthUrl(Mage::getUrl('*'));
			$this->getResponse()->setHeader('Login-Required', 'true');
			$this->_redirectUrl(Mage::getUrl('customer/account/login'));
			// $this->norouteAction();
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);

            return;
        }

        return $this;
    }

    public function collectQuote($flag = false)
    {
        $this->getQuote()->setTotalsCollectedFlag($flag);
        $this->getQuote()->collectTotals()->save();
    }

    public function addDataFromSession($data)
    {
        if ($data && array_key_exists('billing', $data)) {
            if (isset($data['billing_address_id']) && $data['billing_address_id']) {
                Mage::helper('securedcheckout/checkout_address')->saveBilling($data['billing'], $data['billing_address_id']);
            }
            if (isset($data['billing']['use_for_shipping'])
                && $data['billing']['use_for_shipping'] == 0
                && isset($data['shipping_address_id'])
            ) {
                Mage::helper('securedcheckout/checkout_address')->saveShipping($data['shipping'], $data['shipping_address_id']);
            }
        }
    }

    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    protected function _init()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('securedcheckout/config')->getCheckoutTitle());
        $this->_initLayoutMessages('customer/session');
    }

    public function indexAction()
    {
        $isEnabled = Mage::helper('securedcheckout/config')->isEnabled();
        if (!$isEnabled) {
            $error = $this->__('The one step checkout is disabled.');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');

            return;
        }
        /*reference from app/code/core/Mage/Checkout/controllers/OnepageController.php:189*/
        $quote = $this->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');

            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');

            return;
        }

        if (Mage::helper('persistent/session')->isPersistent()) {
            Mage::helper('persistent/session')->getSession()->removePersistentCookie();
            /** @var $customerSession Mage_Customer_Model_Session */
            $customerSession = Mage::getSingleton('customer/session');
            if (!$customerSession->isLoggedIn()) {
                $customerSession->setCustomerId(null)
                    ->setCustomerGroupId(null);
            }

            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quote->getPaymentsCollection()->walk('delete');
            $quote->getAddressesCollection()->walk('delete');
            $quote
                ->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
                ->setIsActive(true)
                ->setCustomerId(null)
                ->setCustomerEmail(null)
                ->setCustomerFirstname(null)
                ->setCustomerMiddlename(null)
                ->setCustomerLastname(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
                ->setIsPersistent(false)
                ->removeAllAddresses();
            //Create guest addresses
            $quote->getShippingAddress();
            $quote->getBillingAddress();
            $quote->collectTotals()->save();

            $this->_redirectUrl(Mage::helper('core/url')->getCurrentUrl());

            return;
        }

        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();
        //Set billing and shipping data from session
        $currentData = $this->getCheckoutSession()->getData('securedcheckout_form_values');
        $this->addDataFromSession($currentData);

        $this->initAddress();
        $this->initShippingMethod();
        $this->initPaymentMethod();

        $this->collectQuote();
        $this->loadLayout();
        $this->_init();
        $this->renderLayout();
    }


    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getOnepage()->getQuote();
    }

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    protected function _showForUnregisteredUsers()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn()
//        || $this->getRequest()->getActionName() == 'index'
        || Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote())
        || !Mage::helper('securedcheckout')->isCustomerMustBeLogged();
    }

    public function initAddress()
    {
        $helperAddress = Mage::helper('securedcheckout/checkout_address');
        if ($this->getQuote()->getBillingAddress()->getCustomerAddressId()) {
            $data = array(
                'use_for_shipping' => true,
            );
            $helperAddress->saveBilling($data, $this->getQuote()->getBillingAddress()->getCustomerAddressId());
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(false);
            $this->getQuote()->collectTotals();
            $this->getQuote()->save();

            return;
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($primaryBillingAddress = $customer->getPrimaryBillingAddress()) {
            $customerAddressId = $primaryBillingAddress->getId();
            $data              = array(
                'use_for_shipping' => true,
            );
            $helperAddress->saveBilling($data, $customerAddressId);
		} else if (is_null($this->getQuote()->getBillingAddress()->getCountryId())){
			$data = array(
				'country_id'       => Mage::helper('securedcheckout/config')->getDefaultCountryId(),
				'use_for_shipping' => true,
			);
			$helperAddress->saveBilling($data);
		} // Brian
//        } else {
//            if (!is_null($this->getQuote()->getBillingAddress()->getId())) {
//                return;
//            }
//            $data = array(
//                'country_id'       => Mage::helper('securedcheckout/config')->getDefaultCountryId(),
//                'use_for_shipping' => true,
//            );
//            $helperAddress->saveBilling($data);
//        }
    }

    /**
     * set shipping method for first load checkout page
     */
    public function initShippingMethod()
    {
        Mage::helper('securedcheckout/checkout_address')->setDefaultShippingMethod($this->getShippingAddress());
    }

    /**
     * set shipping method for first load checkout page
     */
    public function initPaymentMethod()
    {
        $helper = Mage::helper('securedcheckout/checkout_payment');
        // check if payment saved to quote
        if (!$this->getQuote()->getPayment()->getMethod()) {
            $data           = array();
            $paymentMethods = $helper->getPaymentMethods();
            if ((count($paymentMethods) == 1)) {
                $currentPaymentMethod = current($paymentMethods);
                $data['method']       = $currentPaymentMethod->getCode();
            } elseif ($lastPaymentMethod = $helper->getLastPaymentMethod()) {
                $data['method'] = $lastPaymentMethod;
            } elseif ($defaultPaymentMethod = Mage::helper('securedcheckout/config')->getDefaultPaymentMethod()) {
                $data['method'] = $defaultPaymentMethod;
            }
            if (!empty($data)) {
                try {
                    $this->getOnepage()->savePayment($data);
                } catch (Exception $e) {
                    // catch this exception
                }
            }
        }
    }

    public function addProductAction()
    {
        $products   = Mage::getModel('catalog/product')
            ->getCollection();
        $product_id = null;
        foreach ($products as $product) {
            $stock_item = $product->getStockItem();
            if ($stock_item && $stock_item->getIsInStock() == 1) {
                $product_id = $product->getId();
                break;
            }
        }
        $cart = Mage::getSingleton('checkout/cart');
        try {
            $cart->addProduct(Mage::getModel('catalog/product')->load($product_id));
            $cart->save();
        } catch (Exception $e) {
        }
        $this->_redirect('onestepcheckout');

        return;
    }
}