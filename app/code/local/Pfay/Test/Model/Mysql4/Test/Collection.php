<?php
/**
 * Created by PhpStorm.
 * User: Ujwala
 * Date: 27-07-2015
 * Time: 18:02
 */
class Pfay_Test_Model_Mysql4_Test_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('test/test');
    }
}