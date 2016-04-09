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
class Magecheckout_SecuredCheckout_Model_System_Config_Source_Layout
{
    const ONE_COLUMN = '1column';
    const TWO_COLUMNS = '2columns';
    const THREE_COLUMNS = '3columns';
    const THREE_COLUMNS_NO_COLSPAN = '3columns-no-colspan';

    public function toOptionArray()
    {
        $options = array();

        $options[] = array(
            'label' => Mage::helper('securedcheckout')->__('1 Column'),
            'value' => self::ONE_COLUMN
        );
        $options[] = array(
            'label' => Mage::helper('securedcheckout')->__('2 Columns'),
            'value' => self::TWO_COLUMNS
        );
        $options[] = array(
            'label' => Mage::helper('securedcheckout')->__('3 Columns'),
            'value' => self::THREE_COLUMNS
        );
//        $options[] = array(
//            'label' => Mage::helper('securedcheckout')->__('New 3 Columns - Optimized Conversion'),
//            'value' => self::THREE_COLUMNS_NO_COLSPAN
//        );

        return $options;
    }
}
