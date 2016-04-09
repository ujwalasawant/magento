<?php
/**
 * MageCheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageCheckout.com license that is
 * available through the world-wide-web at this URL:
 * http://magecheckout.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageCheckout
 * @package     MageCheckout_CheckoutPromotion
 * @copyright   Copyright (c) 2015 MageCheckout (http://magecheckout.com/)
 * @license     http://magecheckout.com/license-agreement/
 */

/**
 *
 * @category    MageCheckout
 * @package     Magecheckout_SecuredCheckout
 * @author      MageCheckout Developer
 */
class Magecheckout_SecuredCheckout_Model_Status extends Varien_Object
{
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED  => Mage::helper('securedcheckout')->__('Active'),
            self::STATUS_DISABLED => Mage::helper('securedcheckout')->__('Inactive')
        );
    }


    public function toOptionArray()
    {
        return self::getOptionHash();
    }


    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }

        return $options;
    }
}