<?php
/*
 * New product Extension Developed by Magehit
 */
?>
<?php

class Magehit_Newproducts_Block_Newproducts extends Mage_Core_Block_Template {

    public function getNewProducts($limit) {

        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getResourceModel('catalog/product_collection')
                            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                            ->addAttributeToSelect('*') //Need this so products show up correctly in product listing
                            ->addAttributeToFilter('news_from_date', array('or'=> array(
                                0 => array('date' => true, 'to' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter('news_to_date', array('or'=> array(
                                0 => array('date' => true, 'from' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToFilter(
                                array(
                                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                                    )
                              )
                            ->addAttributeToSort('news_from_date', 'desc')
                            ->addMinimalPrice()
                            ->addTaxPercents()
                            ->addStoreFilter(); 
                            
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

        // // CategoryFilter
        // $Category = Mage::getModel('catalog/category')->load($this->getCategoryId());
        // $collection->addCategoryFilter($Category);

        // getNumProduct
        $collection->setPageSize($limit);
        return $collection;
    }

    public function getStoreconfig() {
        $enable = Mage::getStoreConfig('newproducts/genneral_setting/enabled');
        
		//horizontal_carousels_setting
		$horizontal_carousels_setting_title = Mage::getStoreConfig('newproducts/horizontal_carousels_setting/title');
        $horizontal_carousels_setting_limit = Mage::getStoreConfig('newproducts/horizontal_carousels_setting/product_no');
        $horizontal_carousels_setting_slide_itemsonpage = Mage::getStoreConfig('newproducts/horizontal_carousels_setting/slide_itemsonpage');
        $horizontal_carousels_setting_slide_auto = Mage::getStoreConfig('newproducts/horizontal_carousels_setting/slide_auto');
        $horizontal_carousels_setting_slide_navigation = Mage::getStoreConfig('newproducts/horizontal_carousels_setting/slide_navigation');
		
		//vertical_carousels_setting
		$vertical_carousels_setting_title = Mage::getStoreConfig('newproducts/vertical_carousels_setting/title');
        $vertical_carousels_setting_limit = Mage::getStoreConfig('newproducts/vertical_carousels_setting/product_no');
        $vertical_carousels_setting_slide_itemsonpage = Mage::getStoreConfig('newproducts/vertical_carousels_setting/slide_itemsonpage');
        $vertical_carousels_setting_slide_auto = Mage::getStoreConfig('newproducts/vertical_carousels_setting/slide_auto');
        $vertical_carousels_setting_slide_navigation = Mage::getStoreConfig('newproducts/vertical_carousels_setting/slide_navigation');
		
		
        $featuredValues = array(
			//Genneral setting
			'enabled' => $enable,
			//horizontal_carousels_setting
			'horizontal_carousels_setting_title' => $horizontal_carousels_setting_title,
			'horizontal_carousels_setting_limit' => $horizontal_carousels_setting_limit,
			'horizontal_carousels_setting_slide_itemsonpage' => $horizontal_carousels_setting_slide_itemsonpage,
			'horizontal_carousels_setting_slide_auto' => $horizontal_carousels_setting_slide_auto,
			'horizontal_carousels_setting_slide_navigation' => $horizontal_carousels_setting_slide_navigation,
			//vertical_carousels_setting
			'vertical_carousels_setting_title' => $vertical_carousels_setting_title,
			'vertical_carousels_setting_limit' => $vertical_carousels_setting_limit,
			'vertical_carousels_setting_slide_itemsonpage' => $vertical_carousels_setting_slide_itemsonpage,
			'vertical_carousels_setting_slide_auto' => $vertical_carousels_setting_slide_auto,
			'vertical_carousels_setting_slide_navigation' => $vertical_carousels_setting_slide_navigation,
			
		);
        return $featuredValues;
    }

}

?>
