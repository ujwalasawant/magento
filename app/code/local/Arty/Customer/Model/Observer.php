<?php
/**
 * Created by PhpStorm.
 * User: Ujwala
 * Date: 19-10-2015
 * Time: 15:22
 */
class Arty_Customer_Model_Observer extends Mage_Core_Model_Abstract
{
    public function customlink(Varien_Event_Observer $observer)
    {
        $update = $observer->getEvent()->getLayout()->getUpdate();
        $update->addHandle('customer_new_handle');
    }
}