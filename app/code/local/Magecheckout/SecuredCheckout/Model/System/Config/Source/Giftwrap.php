<?php
/**
 * MageCheckout
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
 * @copyright   Copyright (c) 2015 Magecheckout (http://magecheckout.com/)
 * @license     http://magecheckout.com/license-agreement.html
 */
class Magecheckout_SecuredCheckout_Model_System_Config_Source_Giftwrap extends Mage_Core_Model_Abstract
{
    const PER_ORDER = 0;
    const PER_ITEM  = 1;

    public function toOptionArray()
    {
        return array(
            self::PER_ORDER => Mage::helper('securedcheckout')->__('Per Order'),
            self::PER_ITEM  => Mage::helper('securedcheckout')->__('Per Item')
        );
    }
}