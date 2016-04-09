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
class Magecheckout_SecuredCheckout_Block_Checkout_Address_Billing extends Magecheckout_SecuredCheckout_Block_Checkout_Address
{
    /**
     * @param $attribute_code
     * @param $entity_type
     * @return mixed
     */
    public function getAttributeLabel($attribute_code, $entity_type)
    {
        return Mage::helper('securedcheckout')->getAttributeFrontendLabel($attribute_code, $entity_type);
    }

    public function getBillingTriggerElements()
    {
        $triggers = array();
        foreach ($this->getAddressTriggerElements() as $element) {
            $triggers[] = 'billing:' . $element;
        }

        return Mage::helper('core')->jsonEncode($triggers);
    }

}