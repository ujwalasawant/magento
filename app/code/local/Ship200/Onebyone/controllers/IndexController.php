<?php
class Ship200_Onebyone_IndexController extends Mage_Core_Controller_Front_Action
{
    public function postbackAction(){

        $secret_key = Mage::getStoreConfig('onebyone/info/appkey');
        $order_status_tracking = Mage::getStoreConfig('onebyone/info/order_status_tracking');
        $notify_customer_setting = 1;

        if($secret_key == ""){ echo "The Secret Key was never setup. Please refer to read_me file"; exit;}

        if($order_status_tracking == ""){ echo "Please Select The Order Status From Admin For Update With Tracking"; exit;}

        #Extra security
        // Check that request is coming from Ship200 Server
        $allowed_servers = file_get_contents('http://www.ship200.com/instructions/allowed_servers.txt');
        if (!$allowed_servers) $allowed_servers = '173.192.194.99,173.192.194.98,108.58.55.190,45.33.71.107,45.33.73.63,45.33.89.56,45.33.85.63,97.107.136.135,45.79.162.158,45.79.136.18,45.79.130.178';
        $servers_array = explode(",",$allowed_servers);

        $server = 0;
        foreach($servers_array as $ip)
            if($_SERVER['REMOTE_ADDR'] == $ip)
                $server = 1;

        if($server == 0){ echo "Incorrect Server"; exit;}
        // Check that request is coming from Ship200 Server

        #DEBUG
/*        $_POST['update_tracking'] = 100000200  ;
        $_POST['keyForUpdate'] = 100000200  ;
        $_POST['carrier'] = 'USPS';
        $_POST['service'] = 'Priority Mail';
        $_POST['tracking'] = '123456789';*/

        $update_tracking = $this->getRequest()->getParam('update_tracking');
        $id = $this->getRequest()->getParam('id');
        if(isset($update_tracking) && isset($id) && $id == $secret_key){

            $order = Mage::getModel("sales/order")->loadByIncrementId($this->getRequest()->getParam('keyForUpdate'));

            $customerEmailComments = 'Your Order Has Been Shipped';

            if (!$order->getId()) {
                echo "Order does not exist, for the Shipment process to complete";
                exit();
            }

            if ($order->canShip()) {
                try {
                    $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($this->_getItemQtys($order));

                    $shipmentCarrierCode = strtolower($this->getRequest()->getParam('carrier'));
                    $shipmentCarrierTitle = $this->getRequest()->getParam('service')." - (Ship200 OneByOne)";

                    $arrTracking = array(
                        'carrier_code' => isset($shipmentCarrierCode) ? $shipmentCarrierCode : $order->getShippingCarrier()->getCarrierCode(),
                        'title' => isset($shipmentCarrierTitle) ? $shipmentCarrierTitle : $order->getShippingCarrier()->getConfigData('title'),
                        'number' => $this->getRequest()->getParam('tracking'),
                    );

                    $track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
                    $shipment->addTrack($track);

                    $shipment->register();

                    $this->_saveShipment($shipment, $order, $customerEmailComments);

                    $this->_saveOrder($order, $order_status_tracking);

                    echo "Tracking Number: Inserted";
                } catch (Exception $e) {
                    var_dump($e);
                    exit();
                }
            }else{
                echo "Failed to Update Tracking, Order maybe already shipped";
            }
        }else
            echo "Failed to Update Tracking";

        exit();
    }

    protected function _getItemQtys(Mage_Sales_Model_Order $order)
    {
        $qty = array();

        foreach ($order->getAllItems() as $_eachItem) {
            if ($_eachItem->getParentItemId()) {
                $qty[$_eachItem->getParentItemId()] = $_eachItem->getQtyOrdered();
            } else {
                $qty[$_eachItem->getId()] = $_eachItem->getQtyOrdered();
            }
        }

        return $qty;
    }

    protected function _saveShipment(Mage_Sales_Model_Order_Shipment $shipment, Mage_Sales_Model_Order $order, $customerEmailComments = '')
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($order)
            ->save();

        /*$emailSentStatus = $shipment->getData('email_sent');
        if (!$emailSentStatus) {
            $shipment->sendEmail(true, $customerEmailComments);
            $shipment->setEmailSent(true);
        }*/

        return $this;
    }

    protected function _saveOrder(Mage_Sales_Model_Order $order, $status)
    {
        $order->setData('state', $status);
        $order->setData('status', $status);

        $order->save();

        return $this;
    }
}