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
class Magecheckout_SecuredCheckout_Adminhtml_Securedcheckout_System_Config_AjaxController extends Mage_Adminhtml_Controller_Action
{
    /**
     * get Magento Variables
     */
    public function getVariablesAction()
    {
        $customVariables       = Mage::getModel('core/variable')->getVariablesOptionArray(true);
        $storeContactVariables = Mage::getModel('core/source_email_variables')->toOptionArray(true);
        $variables             = array($storeContactVariables, $customVariables);

        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }
}