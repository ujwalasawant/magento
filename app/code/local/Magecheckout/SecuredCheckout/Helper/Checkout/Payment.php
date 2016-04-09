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
class Magecheckout_SecuredCheckout_Helper_Checkout_Payment extends Mage_Core_Helper_Data
{
    /**
     * get Payment Method
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $paymentBlock = Mage::app()->getLayout()->createBlock('securedcheckout/checkout_payment_methods');

        return $paymentBlock->getMethods();
    }

    /**
     * @return Last Payment Method
     */
    public function getLastPaymentMethod()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer->getId()) {
            return false;
        }
        $collection = Mage::getModel('sales/order')
            ->getCollection()
            ->addFilter('customer_id', $customer->getId())
            ->addAttributeToSort('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC)
            ->setPageSize(1);

        $lastOrder = $collection->getFirstItem();
        if (!$lastOrder->getId()) {
            return false;
        }

        return $lastOrder->getPayment()->getMethod();
    }

    public function isEnabledSagePaySuite()
    {
        return ($this->isModuleEnabled('Ebizmarts_SagePaySuite')
            && $this->isModuleOutputEnabled('Ebizmarts_SagePaySuite'));
    }

    public function isSagePaySuiteMethod($paymentMethod)
    {
        return $this->isEnabledSagePaySuite()
        && Mage::helper('sagepaysuite')->isSagePayMethod($paymentMethod);
    }
}