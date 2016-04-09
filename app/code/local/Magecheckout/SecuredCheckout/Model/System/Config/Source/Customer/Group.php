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
class Magecheckout_SecuredCheckout_Model_System_Config_Source_Customer_Group
{
    const CUSTOMER_GROUP_ALL            = 'ALL';
    const CUSTOMER_GROUP_NOT_REGISTERED = 'NOT_REGISTERED';

    public function toOptionArray()
    {
        $res   = Mage::helper('customer')->getGroups()->toOptionArray();
        $found = false;
        foreach ($res as $group) {
            if ($group['value'] == 0) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            array_unshift(
                $res,
                array(
                    'value' => self::CUSTOMER_GROUP_NOT_REGISTERED,
                    'label' => Mage::helper('securedcheckout')->__('Not registered')
                )
            );
        }

        array_unshift(
            $res, array('value' => self::CUSTOMER_GROUP_ALL, 'label' => Mage::helper('securedcheckout')->__('All groups'))
        );

        return $res;
    }
}