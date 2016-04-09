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
 * @package     Magecheckout_SecuredCheckout
 * @copyright   Copyright (c) 2015 MageCheckout (http://magecheckout.com/)
 * @license     http://magecheckout.com/license-agreement/
 */


class Magecheckout_SecuredCheckout_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('securedcheckout/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('securedcheckout/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = array('value' => 'securedcheckout/rule_condition_address|' . $code, 'label' => $label);
        }

        $helper = Mage::helper('securedcheckout');
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
                 array(
                     'value' => 'securedcheckout/rule_condition_product_found',
                     'label' => $helper->__('Product attributes combination'),
                 ),
                 array(
                     'value' => 'securedcheckout/rule_condition_product_subselect',
                     'label' => $helper->__('Products subselection'),
                 ),
                 array(
                     'value' => 'securedcheckout/rule_condition_combine',
                     'label' => $helper->__('Conditions combination'),
                 ),
                 array(
                     'value' => $attributes,
                     'label' => $helper->__('Cart Attribute'),
                 ),
            )
        );
        return $conditions;
    }
}