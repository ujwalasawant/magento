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
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 * @author      Magecheckout Developer
 */
class Magecheckout_SecuredCheckout_Model_Total_Invoice_Giftwrap extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect total when create Invoice
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getMcGiftwrapAmount() < 0.0001) {
            return;
        }
        $invoice->setMcGiftwrapBaseAmount(0);
        $invoice->setMcGiftwrapAmount(0);
        $totalGiftwrapAmount     = 0;
        $totalGiftwrapBaseAmount = 0;
        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }
            $itemQty       = $item->getQty();
            $giftwrapBaseAmount = $orderItem->getMcGiftwrapBaseAmount() * $itemQty;
            $giftwrapAmount     = $orderItem->getMcGiftwrapAmount() * $itemQty;
            $item->setMcGiftwrapBaseAmount($giftwrapBaseAmount);
            $item->setMcGiftwrapAmount($giftwrapAmount);
            $totalGiftwrapBaseAmount += $giftwrapBaseAmount;
            $totalGiftwrapAmount += $giftwrapAmount;
        }
        $invoice->setMcGiftwrapBaseAmount($totalGiftwrapBaseAmount);
        $invoice->setMcGiftwrapAmount($totalGiftwrapAmount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $totalGiftwrapAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $totalGiftwrapBaseAmount);

        return $this;
    }

}
