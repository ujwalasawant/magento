<?php
/**
 * Created by PhpStorm.
 * User: Ujwala
 * Date: 27-07-2015
 * Time: 17:50
 */

class Pfay_Test_Model_Mysql4_Test extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('test/test','id_pfay_test');
    }
}