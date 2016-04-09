<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/Block/Adminhtml/System/Config/Fieldset/License.php
*  Copyright: Copyright (c) 2010-2015 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.0.3
*  Date: 2015-10-09
*  Review: 2015-10-09
*  URL: http://www.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=modules&module=magento
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2015 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/

class Ax_Zoom_Block_Adminhtml_System_Config_Fieldset_License extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function _prepareToRender()
    {
        $this->addColumn('domain', array(
            'label' => Mage::helper('axzoom')->__('Domain'),
            'style' => 'width:200px'
        ));
        $this->addColumn('type', array(
            'label' => Mage::helper('axzoom')->__('License Type'),
            'renderer' => 'bla'
        ));

        $this->addColumn('license', array(
            'label' => Mage::helper('axzoom')->__('License Key'),
            'style' => 'width:200px'
        ));

        $this->addColumn('error200', array(
            'label' => Mage::helper('axzoom')->__('Error200'),
            'style' => 'width:100px'
        ));

        $this->addColumn('error300', array(
            'label' => Mage::helper('axzoom')->__('Error300'),
            'style' => 'width:100px'
        ));
 
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('axzoom')->__('Add');
    }

    protected function _renderCellTemplate($columnName)
    {    
        if ($columnName == 'type') {
            $el = $this->getElement();
            
            $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
            $rendered = '<select id="licType" name="' . $inputName . '">';
            $rendered .= '<option value="evaluation" #{option_extra_attr_evaluation}>evaluation</option>';
            $rendered .= '<option value="developer" #{option_extra_attr_developer}>developer</option>';
            $rendered .= '<option value="basic" #{option_extra_attr_basic}>basic</option>';
            $rendered .= '<option value="standard" #{option_extra_attr_standard}>standard</option>';
            $rendered .= '<option value="business" #{option_extra_attr_business}>business</option>';
            $rendered .= '<option value="corporate" #{option_extra_attr_corporate}>corporate</option>';
            $rendered .= '<option value="enterprise" #{option_extra_attr_enterprise}>enterprise</option>';
            $rendered .= '<option value="unlimited" #{option_extra_attr_unlimited}>unlimited</option>';
            $rendered .= '</select>';
            
            return $rendered;
        }
        return parent::_renderCellTemplate($columnName);
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $row->getData('type'),
            'selected="selected"'
        );
    }    
}
?>