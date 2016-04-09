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
class Magecheckout_SecuredCheckout_Model_System_Config_Source_Giftmessage
{
    const DISABLED_CODE = 0;
    const SHIPPING_METHOD_SECTION = 1;
    const REVIEW_SECTION = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::DISABLED_CODE,
                'label' => Mage::helper('securedcheckout')->__('Disabled'),
            ),
            array(
                'value' => self::SHIPPING_METHOD_SECTION,
                'label' => Mage::helper('securedcheckout')->__('Show In Shipping Methods Section'),
            ),
            array(
                'value' => self::REVIEW_SECTION,
                'label' => Mage::helper('securedcheckout')->__('Show In Review Section'),
            ),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::ENABLED_CODE  => Mage::helper('securedcheckout')->__(self::ENABLED_LABEL),
            self::DISABLED_CODE => Mage::helper('securedcheckout')->__(self::DISABLED_LABEL),
        );
    }
}