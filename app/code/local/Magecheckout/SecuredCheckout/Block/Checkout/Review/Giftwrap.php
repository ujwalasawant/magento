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
class Magecheckout_SecuredCheckout_Block_Checkout_Review_Giftwrap extends Mage_Core_Block_Template
{
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('securedcheckout/config');

        return parent::__construct();
    }

    public function canShow()
    {
        if (!$this->_helper->isEnabledGiftWrap()) {
            return false;
        }

        return true;
    }

    /**
     *
     * @return string
     */
    public function getAddGiftWrapUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/addGiftWrap', array('_secure' => true));
    }

    /**
     * @return mixed
     */
    public function isUsedGiftwrap()
    {
        return Mage::getSingleton('checkout/session')->getData('is_used_giftwrap');
    }

}