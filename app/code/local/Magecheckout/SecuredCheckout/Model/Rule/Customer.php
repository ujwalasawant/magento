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


class Magecheckout_SecuredCheckout_Model_Rule_Customer extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('securedcheckout/rule_customer');
    }
}