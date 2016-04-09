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
class Magecheckout_SecuredCheckout_Block_Addition_Login_Form extends Magecheckout_SecuredCheckout_Block_Container
{
    public function canShow()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return false;
        }

        return true;
    }

    public function getLoginUrl()
    {

        return Mage::getUrl('onestepcheckout/checkout/login', array('_secure' => $this->isSecure()));
    }

    public function getForgotUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/forgotPassword', array('_secure' => $this->isSecure()));
    }

}