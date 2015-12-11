<?php
/**
 * Created by PhpStorm.
 * User: Ujwala
 * Date: 13-10-2015
 * Time: 18:08
 */
class Arty_Customer_Model_Entity_Status extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();
            $this->_options[] = array(
                'value' => '',
                'label' => 'Choose Option..'
            );
            $this->_options[] = array(
                'value' => 1,
                'label' => 'Married'
            );
            $this->_options[] = array(
                'value' => 2,
                'label' => 'Single'
            );


        }

        return $this->_options;
    }

}