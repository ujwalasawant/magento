<?php

class Magehit_AjaxSearch_Block_Adminhtml_System_Config_Searchblocklabel1 extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '
        <div style="font-size:14px;margin-left:-205px;margin-top:10px;margin-bottom:5px;color:red; border-bottom:1px solid red;"><b>' . $this->__("Search box Configuration") . '</b></div>
        ';
    }

}