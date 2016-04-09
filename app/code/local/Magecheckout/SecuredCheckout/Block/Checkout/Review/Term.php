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
class Magecheckout_SecuredCheckout_Block_Checkout_Review_Term extends Mage_Checkout_Block_Agreements
{

    /**
     * enable gift message or not
     *
     */
    public function canShow()
    {
        return count($this->getTermAndConditions());
    }

    public function getTermAndConditions()
    {
        $agreements = array();
        if (Mage::helper('securedcheckout/config')->isEnabledTerm()) {
            $agreementsDefault = $this->getAgreements();
            foreach ($agreementsDefault as $agree) {
                $agreements[] = $agree;
            }
            $content      = Mage::helper('securedcheckout/config')->getTermContent();
            $checkboxText = Mage::helper('securedcheckout/config')->getTermCheckboxText();
            $checkboxName = Mage::helper('securedcheckout/config')->getTermTitle();
            if ($checkboxText && $checkboxName && $content) {
                $agreementConfig = array(
                    'id'            => 'mc_osc_term',
                    'checkbox_text' => $checkboxText,
                    'name'          => $checkboxName,
                    'content'       => $this->_process($content),
                    'is_html'       => true
                );
                $agreements[]    = new Varien_Object($agreementConfig);
            }
        }

        return $agreements;
    }

    protected function _process($text)
    {
        $helper    = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        $processor->setVariables($this->_getVariables());

        return $processor->filter($text);
    }

    private function _getVariables()
    {
        $variables = array();
        if ($productId = $this->getData('product_id')) {
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!is_null($product->getId())) {
                $variables['product'] = $product;
            }
        }

        return $variables;
    }

    public function isRequiredReadTerm()
    {
        return Mage::helper('securedcheckout/config')->isRequiredReadTerm();
    }

    public function getFormData()
    {
        return Mage::getSingleton('checkout/session')->getData('securedcheckout_form_values');
    }

}