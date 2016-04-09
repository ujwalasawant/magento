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
class Magecheckout_SecuredCheckout_Block_Checkout_Form extends Magecheckout_SecuredCheckout_Block_Container
{
    public function getPlaceOrderUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/saveOrder', array('_secure' => $this->isSecure()));
    }

    public function getCheckoutSuccessUrl()
    {
        return Mage::getUrl('checkout/onepage/success', array('_secure' => $this->isSecure()));
    }
}