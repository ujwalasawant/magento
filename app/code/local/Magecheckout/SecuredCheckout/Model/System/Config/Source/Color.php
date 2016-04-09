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
class Magecheckout_SecuredCheckout_Model_System_Config_Source_Color
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '#3399cc', 'label' => Mage::helper('securedcheckout')->__('Default')),
            array('value' => 'orange', 'label' => Mage::helper('securedcheckout')->__('Orange')),
            array('value' => 'green', 'label' => Mage::helper('securedcheckout')->__('Green')),
            array('value' => 'black', 'label' => Mage::helper('securedcheckout')->__('Black')),
            array('value' => 'blue', 'label' => Mage::helper('securedcheckout')->__('Blue')),
            array('value' => 'darkblue', 'label' => Mage::helper('securedcheckout')->__('Dark Blue')),
            array('value' => 'pink', 'label' => Mage::helper('securedcheckout')->__('Pink')),
            array('value' => 'red', 'label' => Mage::helper('securedcheckout')->__('Red')),
            array('value' => 'violet', 'label' => Mage::helper('securedcheckout')->__('Violet')),
            array('value' => 'custom', 'label' => Mage::helper('securedcheckout')->__('Custom')),
        );
    }
}
