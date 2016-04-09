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
class Magecheckout_SecuredCheckout_Helper_Checkout_Newsletter extends Mage_Core_Helper_Data
{

    public function isMageNewsletterEnabled()
    {
        return $this->isModuleEnabled('Mage_Newsletter') && $this->isModuleOutputEnabled('Mage_Newsletter');
    }

    public function subscribeCustomer($data = array())
    {
        Mage::getModel('newsletter/subscriber')->subscribe($data['email']);
    }
}