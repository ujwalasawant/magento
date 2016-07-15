<?php

class Magehit_AjaxSearch_Model_Mysql4_Product_Collection extends Mage_CatalogSearch_Model_Resource_Search_Collection
{
    /**
     * Retrieve collection of all attributes
     *
     * @return Varien_Data_Collection_Db
     */
    protected function _getAttributesCollection()
    {
        $attributeCodes = array('name');
        $attributes = Mage::getStoreConfig('ajax_search/general/attributes');
        if ($attributes != '') {
            $attributeCodes = explode(',', $attributes);
        }
        
        if (!$this->_attributesCollection) {
            $this->_attributesCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('attribute_code', array('in' => $attributeCodes))
                ->load();
            
//            foreach ($this->_attributesCollection as $attribute) {
//                $attribute->setEntity($this->getEntity());
//            }
        }
        return $this->_attributesCollection;
    }
}