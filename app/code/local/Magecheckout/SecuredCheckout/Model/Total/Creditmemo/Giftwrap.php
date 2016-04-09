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

/**
 * SecuredCheckout Spend for Order by Point Model
 *
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 * @author      Magecheckout Developer
 */
class Magecheckout_SecuredCheckout_Model_Total_Creditmemo_Giftwrap extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{

    /**
     * Collect total when create Creditmemo
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($creditmemo->getGrandTotal() == 0) {
            $creditmemo->setIsLastCreditmemo(false);
        }
        if ($order->getMcGiftwrapAmount() < 0.0001) {
            return;
        }
        $creditmemo->setMcGiftwrapBaseAmount(0);
        $creditmemo->setMcGiftwrapAmount(0);
        $totalGiftwrapAmount     = 0;
        $totalGiftwrapBaseAmount = 0;
        /** @var $item Mage_Sales_Model_Order_Credimemo_Item */
        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }
            $itemQty            = $item->getQty();
            $giftwrapBaseAmount = $orderItem->getMcGiftwrapBaseAmount() * $itemQty;
            $giftwrapAmount     = $orderItem->getMcGiftwrapAmount() * $itemQty;
            $item->setMcGiftwrapBaseAmount($giftwrapBaseAmount);
            $item->setMcGiftwrapAmount($giftwrapAmount);
            $totalGiftwrapBaseAmount += $giftwrapBaseAmount;
            $totalGiftwrapAmount += $giftwrapAmount;
        }
        $creditmemo->setMcGiftwrapBaseAmount($totalGiftwrapBaseAmount);
        $creditmemo->setMcGiftwrapAmount($totalGiftwrapAmount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $totalGiftwrapAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $totalGiftwrapBaseAmount);
    }

    /**
     * check credit memo is last or not
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }

        return true;
    }
}
