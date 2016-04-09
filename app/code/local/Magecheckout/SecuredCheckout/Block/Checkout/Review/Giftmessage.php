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
class Magecheckout_SecuredCheckout_Block_Checkout_Review_Giftmessage extends Magecheckout_SecuredCheckout_Block_Container
{
    /**
     * enable gift message or not
     *
     */
    public function canShow()
    {
        $reviewSection = Magecheckout_SecuredCheckout_Model_System_Config_Source_Giftmessage::REVIEW_SECTION;

        return Mage::helper('securedcheckout/checkout_giftmessage')->isEnabled() == $reviewSection;
    }

    public function getGiftMessageUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/saveGiftMessage', array('_secure' => $this->isSecure()));
    }

}