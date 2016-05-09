<?php

/**
 * Created by PhpStorm.
 * User: sneha
 * Date: 6/5/16
 * Time: 5:25 PM
 */
class Arty_Catalog_Helper_Categoryhelper extends Mage_Core_Helper_Abstract {

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