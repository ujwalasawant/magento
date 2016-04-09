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
class Magecheckout_SecuredCheckout_Block_Checkout_Shipping_Available extends Magecheckout_SecuredCheckout_Block_Checkout_Shipping
{
    protected $_rates = null;
    protected $_address = null;

    /**
     * get all shipping rate
     *
     * @return array|null
     */
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->getShippingAddress()->collectShippingRates()->save();
            $groups = $this->getShippingAddress()->getGroupedAllShippingRates();

            return $this->_rates = $groups;
        }
        /* Don't show collect in store rate as an available option. */
        if (!Mage::getStoreConfig('carriers/collectinstore/onestep') && Mage::getStoreConfig('carriers/collectinstore/active') && array_key_exists('collectinstore', $this->_rates)) {
            unset($this->_rates['collectinstore']);
        }

        return $this->_rates;
    }

    /**
     * get Shipping Address From Quote
     *
     * @return Mage_Sales_Model_Quote_Address|null
     */
    public function getShippingAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }

        return $this->_address;
    }

    /**
     * get default shipping method
     *
     * @return mixed
     */
    public function getDefaultShippingMethod()
    {
        return $this->getHelperConfig()->getDefaultShippingMethod();
    }

}