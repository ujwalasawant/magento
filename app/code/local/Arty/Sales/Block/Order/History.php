<?php
class Arty_Sales_Block_Order_History extends Mage_Sales_Block_Order_History
{

    public function getBackUrl()
    {
        return Mage::getUrl('*/*/history');
    }

    public function getInvoices()
    {
        $invoices = Mage::getResourceModel('sales/invoice_collection')->setOrderFilter($this->getOrder()->getId())->load();
        return $invoices;
    }

    public function getPrintUrl()
    {
        return Mage::getUrl('*/*/print');
    }
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Order # %s', $this->getOrder()->getRealOrderId()));
        }
        $this->setChild(
            'payment_info',
            $this->helper('payment')->getInfoBlock($this->getOrder()->getPayment())
        );
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }



    /**
     * Return back title for logged in and guest users
     *
     * @return string
     */


    public function getInvoiceUrl($order)
    {
        return Mage::getUrl('*/*/invoice', array('order_id' => $order->getId()));
    }

    public function getShipmentUrl($order)
    {
        return Mage::getUrl('*/*/shipment', array('order_id' => $order->getId()));
    }

    public function getCreditmemoUrl($order)
    {
        return Mage::getUrl('*/*/creditmemo', array('order_id' => $order->getId()));
    }
}