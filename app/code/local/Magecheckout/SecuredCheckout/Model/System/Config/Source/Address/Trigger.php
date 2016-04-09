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
class Magecheckout_SecuredCheckout_Model_System_Config_Source_Address_Trigger
{


    public function getTriggerOption()
    {
        return array(
            'street1'    => Mage::helper('securedcheckout')->__('Street'),
            'country_id' => Mage::helper('securedcheckout')->__('Country Id'),
            'region'     => Mage::helper('securedcheckout')->__('Region '),
            'region_id'  => Mage::helper('securedcheckout')->__('Region Id'),
            'city'       => Mage::helper('securedcheckout')->__('City'),
            'postcode'   => Mage::helper('securedcheckout')->__('Postcode'),
        );
    }

    public function toOptionArray()
    {

        $options = array();
        foreach ($this->getTriggerOption() as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }

        return $options;
    }
}