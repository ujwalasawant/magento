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
 * SecuredCheckout Total Point Spend Block
 *
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 * @author      Magecheckout Developer
 */
class Magecheckout_SecuredCheckout_Block_Totals_Invoice_Giftwrap extends Mage_Core_Block_Template
{
    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $invoice     = $totalsBlock->getInvoice();
        if ($invoice && $invoice->getMcGiftwrapAmount() > 0.01) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'        => 'mc_giftwrap_label',
                'label'       => $this->__('Gift wrap'),
                'value'       => $invoice->getMcGiftwrapAmount(),
                'is_formated' => false,
            )), 'subtotal');
        }
    }
}
