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
 * @category   Magecheckout
 * @package    Magecheckout_SecuredCheckout
 * @version    3.0.0
 * @copyright   Copyright (c) 2015 Magecheckout (http://www.magecheckout.com/)
 * @license     http://wiki.magecheckout.com/general/license.html
 */
class Magecheckout_SecuredCheckout_Block_Container extends Mage_Checkout_Block_Onepage_Abstract
{
    protected $_helperData;
    protected $_helperConfig;
    protected $_isSecure;

    public function __construct()
    {
        parent::__construct();
        $this->_helperData   = Mage::helper('securedcheckout');
        $this->_helperConfig = Mage::helper('securedcheckout/config');
        $this->_helperBlock  = Mage::helper('securedcheckout/block');
        $this->_isSecure     = Mage::app()->getStore()->isCurrentlySecure();
    }

    /**
     * get helper config
     *
     * @return
     */
    public function getHelperConfig()
    {
        return $this->_helperConfig;
    }

    public function getHelperBlock()
    {
        return $this->_helperBlock;
    }

    public function getHelperData()
    {
        return $this->_helperData;
    }

    /**
     * get current url is http or https
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->_isSecure;
    }

    public function getBlockMapping()
    {
        $blocks       = array();
        $blockMapping = $this->getHelperBlock()->getReloadSection();
        foreach ($blockMapping as $action => $sections) {
            $blocks[$action] = $sections;
        }

        return Mage::helper('core')->jsonEncode($blocks);
    }

    public function getBlockSection()
    {
        $container = new Varien_Object();
        $container->setBlocks($this->getHelperBlock()->getBlocksSection());
        Mage::dispatchEvent('secured_checkout_prepare_block_section_after', array(
            'container' => $container
        ));

        return Mage::helper('core')->jsonEncode($container->getBlocks());
    }

    public function getNumbering($increment = true)
    {
        return Mage::helper('securedcheckout')->getNumbering($increment);
    }

    public function isNoColspanLayout()
    {
        return $this->_helperConfig->getDesignConfig('page_layout') == '3columns-no-colspan';
    }

    public function getGrandTotal()
    {
        return Mage::helper('securedcheckout')->getGrandTotal($this->getQuote());
    }

    public function showGrandTotal()
    {
        return $this->getHelperConfig()->showGrandTotal();
    }

    /**
     * Checkout title config
     *
     * @return string
     */
    public function getCheckoutTitle()
    {
        return $this->escapeHtml($this->getHelperConfig()->getCheckoutTitle());
    }

    /**
     * Checkout description config
     *
     * @return mixed
     */
    public function getCheckoutDescription()
    {
        return $this->getHelperConfig()->getCheckoutDescription();
    }

    public function allowShipToDifferent()
    {
        return $this->getHelperConfig()->allowShipToDifferent();
    }

    /**
     * @return string
     */
    public function getChangeAddressUrl()
    {

        return Mage::getUrl('onestepcheckout/checkout/saveAddressTrigger', array('_secure' => $this->isSecure()));
    }

    /**
     * @return string
     */
    public function getSaveFormUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/saveForm', array('_secure' => $this->isSecure()));
    }

    /**
     * get save shipping method url
     *
     * @return string
     */
    public function getSaveShippingMethodUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/saveShippingMethod', array('_secure' => $this->isSecure()));
    }

    /**
     * @return string
     */
    public function getSavePaymentUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/savePayment', array('_secure' => $this->isSecure()));
    }

    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }

    public function getApplyCouponAjaxUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/saveCoupon', array('_secure' => $this->isSecure()));
    }

    public function getCancelCouponAjaxUrl()
    {
        return Mage::getUrl('onestepcheckout/checkout/cancelCoupon', array('_secure' => $this->isSecure()));
    }

    public function getFormData()
    {
        return Mage::getSingleton('checkout/session')->getData('securedcheckout_form_values');
    }

    public function getCommentsData()
    {
        $data = Mage::getSingleton('checkout/session')->getData('securedcheckout_form_values');
        if (isset($data['comments'])) {
            return $data['comments'];
        }

        return '';
    }

    /**
     * get Customer Name
     *
     * @return string
     */
    public function getUsername()
    {
        $username = Mage::getSingleton('customer/session')->getUsername(true);

        return $this->escapeHtml($username);
    }

    /**
     *
     */
    public function getActionPattern()
    {
        $actionPattern = '/securecheckout\/checkout\/([^\/]+)\//';

        return $actionPattern;
    }


    public function isEnabledMorphEffect()
    {
        return $this->getHelperConfig()->isEnabledMorphEffect();
    }
}