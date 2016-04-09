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

/**
 *
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 * @author      Magecheckout Developer
 */
class Magecheckout_SecuredCheckout_Model_Total_Quote_Giftwrap
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    /**
     * collect reward points that customer earned (per each item and address) total
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote         $quote
     * @return Magecheckout_SecuredCheckout_Model_Total_Quote_Point
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $quote           = $address->getQuote();
        $_giftWrapHelper = Mage::helper('securedcheckout/checkout_giftwrap');
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');
        if (!$_giftWrapHelper->isEnabled($quote->getStoreId()) || !$session->getData('is_used_giftwrap')) {
            return $this;
        }
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        $giftWrapBaseAmount = $_giftWrapHelper->getGiftWrapAmount($quote);
        $giftWrapAmount     = $quote->getStore()->convertPrice($giftWrapBaseAmount);
        if ($giftWrapAmount > 0) {
            $address->setMcGiftwrapBaseAmount($giftWrapBaseAmount);
            $address->setMcGiftwrapAmount($giftWrapAmount);
            $address->setBaseGrandTotal($address->getGrandTotal() + $giftWrapBaseAmount);
            $address->setGrandTotal($address->getGrandTotal() + $giftWrapAmount);
        }
        Mage::dispatchEvent('securedcheckout_collect_total_giftwrap_before', array(
            'address' => $address,
        ));

        return $this;
    }

    /**
     * fetch
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this|array
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getMcGiftwrapAmount();
        if ($amount != 0) {
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => Mage::helper('sales')->__('Gift Wrap'),
                'value' => $amount
            ));
        }

        return $this;
    }
}
