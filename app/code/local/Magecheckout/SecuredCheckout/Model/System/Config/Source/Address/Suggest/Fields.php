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
class Magecheckout_SecuredCheckout_Model_System_Config_Source_Address_Suggest_Fields
{


    public function getFieldsOption()
    {
        return array(
            ''       => Mage::helper('securedcheckout')->__('No'),
            'google' => Mage::helper('securedcheckout')->__('Google'),
            'pca'    => Mage::helper('securedcheckout')->__('Capture+'),
        );
    }

    public function toOptionArray()
    {

        $options = array();
        foreach ($this->getFieldsOption() as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }

        return $options;
    }
}