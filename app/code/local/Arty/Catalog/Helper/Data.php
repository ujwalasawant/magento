<?php
/**
 * Created by PhpStorm.
 * User: ujwala
 * Date: 16/1/16
 * Time: 3:39 PM
 */ 
class Arty_Catalog_Helper_Data extends Mage_Core_Helper_Abstract {
    public function canShowheader($category)
    {
        if (is_int($category)) {
            $category = Mage::getModel('catalog/category')->load($category);
        }

        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }
        if (!$category->isInRootCategoryList()) {
            return false;
        }
        if(!$category->getCollection()){
            return false;
        }
        return true;
    }
}