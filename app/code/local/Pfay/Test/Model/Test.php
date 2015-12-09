<?php
/**
 * Created by PhpStorm.
 * User: Ujwala
 * Date: 27-07-2015
 * Time: 17:51
 */
class Pfay_Test_Model_Test extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('test/test');
    }
}