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
class Magecheckout_SecuredCheckout_Helper_Checkout_Giftwrap extends Mage_Core_Helper_Data
{
    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return Mage::helper('securedcheckout/config')->isEnabledGiftWrap($store);
    }

    /**
     * get current checkout quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }

        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getGiftWrapAmount($quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->getQuote();
        }
        $items       = $quote->getAllItems();
        $total_items = 0;
        foreach ($items as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                continue;
            }
            $total_items += $item->getQty();
        }
        $giftWrapType   = Mage::helper('securedcheckout/config')->getGiftWrapType();
        $giftWrapAmount = Mage::helper('securedcheckout/config')->getOrderGiftwrapAmount();
        if (!$total_items)
            return 0;
        if ($giftWrapType == Magecheckout_SecuredCheckout_Model_System_Config_Source_Giftwrap::PER_ITEM) {
            $giftWrapAmount *= $total_items;
        }
        $this->_addGiftWrapToItems($quote, $giftWrapAmount / $total_items);

        return $giftWrapAmount;
    }

    protected function _addGiftWrapToItems($quote, $giftWrapBaseAmount)
    {
        $items          = $quote->getAllItems();
        $giftWrapAmount = $quote->getStore()->convertPrice($giftWrapBaseAmount);
        foreach ($items as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                continue;
            }
            $item->setMcGiftwrapBaseAmount($item->getMcGiftwrapBaseAmount() + $giftWrapBaseAmount);
            $item->setMcGiftwrapAmount($item->getMcGiftwrapAmount() + $giftWrapAmount);
        }
    }
}