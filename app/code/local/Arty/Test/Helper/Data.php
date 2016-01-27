<?php
/**
 * Created by PhpStorm.
 * User: Sneha
 * Date: 22-09-2015
 * Time: 18:31
 */ 
class Arty_Test_Helper_Data extends Mage_Core_Helper_Abstract {
    public function test()
    {
        $test=Mage::getModel('catalog/product')->getCollection();

    }
}