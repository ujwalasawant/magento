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
class Magecheckout_SecuredCheckout_Helper_Checkout_Shipping extends Mage_Core_Helper_Data
{
    const TEMPLATE_PATH = 'magecheckout/securedcheckout/';
    const EVENT_PREFIX = 'magecheckout_one_step_checkout_';

    /**
     * get shippimg method temple
     *
     * @return string
     */
    public function getShippingMethodTemplate()
    {
        $template = new Varien_Object(array(
            'file_path' => 'checkout/shipping.phtml'
        ));
        Mage::dispatchEvent(self::EVENT_PREFIX . 'get_shipping_method_template_before', array(
            'template' => $template
        ));

        return self::TEMPLATE_PATH . $template->getFilePath();
    }

    public function getShippingRates()
    {
        $address       = $this->getQuote()->getShippingAddress()->collectShippingRates()
            ->save();
        $shippingRates = $address->getGroupedAllShippingRates();

        return $shippingRates;
    }


    public function getLastShippingMethod()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer || !$customer->getId()) {
            return false;
        }
        $lastOrder = $this->getOrderCollection($customer)->getFirstItem();
        if (!$lastOrder || !$lastOrder->getId()) {
            return false;
        }

        return $lastOrder->getShippingMethod();
    }

    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')
            ->getQuote();
    }

    public function getOrderCollection($customer)
    {
        $orderCollection = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('shipping_method', array('neq' => ''))
            ->addFilter('customer_id', $customer->getId())
            ->addAttributeToSort('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC)
            ->setPageSize(1);

        return $orderCollection;
    }

    /**
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
}