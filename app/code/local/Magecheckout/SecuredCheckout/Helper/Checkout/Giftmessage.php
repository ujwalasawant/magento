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
class Magecheckout_SecuredCheckout_Helper_Checkout_Giftmessage extends Mage_GiftMessage_Helper_Message
{
    public function isEnabled($store = null)
    {
        return Mage::helper('securedcheckout/config')->isEnabledGiftMessage($store);
    }

    /**
     *
     */
    public function getInline($type, Varien_Object $entity, $dontDisplayContainer = false)
    {
        if (in_array($type, array('onepage_checkout', 'multishipping_address'))) {
            if (!$this->isMessagesAvailable('order', $entity)) {
                return '';
            }
        } elseif (!$this->isMessagesAvailable($type, $entity)) {
            return '';
        }

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_inline')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer($dontDisplayContainer)
            ->setEntity($entity)
            ->setType($type)
            ->setTemplate('magecheckout/securedcheckout/checkout/review/giftmessage/inline.phtml')
            ->setFormData(Mage::getSingleton('checkout/session')->getData('securedcheckout_form_values'))
            ->toHtml();
    }
}