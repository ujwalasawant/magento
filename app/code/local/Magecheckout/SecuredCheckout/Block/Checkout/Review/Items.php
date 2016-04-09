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
class Magecheckout_SecuredCheckout_Block_Checkout_Review_Items extends Mage_Checkout_Block_Onepage_Review_Info
{
    /**
     * get ajax cart url: (remove item, add a item, sub a item )
     *
     * @return string
     */
    public function getAjaxCartItemUrl()
    {
        $isSecure = Mage::app()->getStore()->isCurrentlySecure();

        return Mage::getUrl('onestepcheckout/checkout/ajaxCartItem', array('_secure' => $isSecure));
    }

    public function getGrandTotal()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        return Mage::helper('securedcheckout')->getGrandTotal($quote);
    }
}