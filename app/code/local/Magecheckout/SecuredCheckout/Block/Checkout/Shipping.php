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
class Magecheckout_SecuredCheckout_Block_Checkout_Shipping extends Magecheckout_SecuredCheckout_Block_Checkout_Address
{
    /**
     * get installed Store Pickup
     *
     * @return bool
     */
    public function isEnabledStorePickup()
    {
        return (Mage::helper('securedcheckout')->isModuleEnabled('Magecheckout_Storepickup')
            && Mage::helper('storepickup')->isEnabled());
    }

    public function isEnabledGiftOptions()
    {
        $shippingMethodSection = Magecheckout_SecuredCheckout_Model_System_Config_Source_Giftmessage::SHIPPING_METHOD_SECTION;

        return Mage::helper('securedcheckout/checkout_giftmessage')->isEnabled() == $shippingMethodSection;
    }

    public function getGiftMessageUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/saveGiftMessage', array('_secure' => $this->isSecure()));
    }
}