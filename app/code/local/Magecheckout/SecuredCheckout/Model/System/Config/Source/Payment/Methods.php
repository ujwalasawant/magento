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
class Magecheckout_SecuredCheckout_Model_System_Config_Source_Payment_Methods
{
    protected static $_methods;

    protected function _getPaymentMethods()
    {
        return $this->getAllMethods();
    }

    public function toOptionArray()
    {
        $methods = array(array('value' => '', 'label' => ''));
        foreach ($this->_getPaymentMethods() as $paymentCode => $paymentModel) {
            $paymentTitle          = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
    }

    // copied from /app/code/core/Mage/Payment/Model/Config.php
    public function getAllMethods($store = null)
    {
        $methods = array();
        $config  = Mage::getStoreConfig('payment', $store);
        foreach ($config as $code => $methodConfig) {
            if (empty($methodConfig['model'])) {
                continue;
            }
            $method = $this->_getMethod($code, $methodConfig);
            if ($method) {
                $methods[$code] = $method;
            }
        }

        return $methods;
    }

    protected function _getMethod($code, $config, $store = null)
    {
        if (isset(self::$_methods[$code])) {
            return self::$_methods[$code];
        }
        $modelName = $config['model'];
        $method    = Mage::getModel($modelName);
        if (!$method) {
            return false;
        }
        $method->setId($code)->setStore($store);
        self::$_methods[$code] = $method;

        return self::$_methods[$code];
    }
}