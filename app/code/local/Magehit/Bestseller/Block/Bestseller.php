<?php

class Magehit_Bestseller_Block_Bestseller extends Mage_Catalog_Block_Product_List {

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    protected function useFlatCatalogProduct()
    {
        return Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
    }    
    public function getBestsellerproduct($limit = 10)     
    { 
        if($limit == "") $limit = 10;
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('bestseller/product_bestseller')
        ->addOrderedQty()
        ->addAttributeToSelect('id')
        ->addAttributeToSelect(array('name', 'price', 'small_image'))
        ->setStoreId($storeId)
        ->addStoreFilter($storeId)
        ->setOrder('ordered_qty', 'desc'); // most best sellers on top
        // getNumProduct
        $collection->setPageSize($limit); // require before foreach
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        return $collection;
    }

    public function getStoreconfig() {
        $enable = Mage::getStoreConfig('bestseller/genneral_setting/enabled');
        
		//horizontal_carousels_setting
		$horizontal_carousels_setting_title = Mage::getStoreConfig('bestseller/horizontal_carousels_setting/title');
        $horizontal_carousels_setting_limit = Mage::getStoreConfig('bestseller/horizontal_carousels_setting/product_no');
        $horizontal_carousels_setting_slide_itemsonpage = Mage::getStoreConfig('bestseller/horizontal_carousels_setting/slide_itemsonpage');
        $horizontal_carousels_setting_slide_auto = Mage::getStoreConfig('bestseller/horizontal_carousels_setting/slide_auto');
        $horizontal_carousels_setting_slide_navigation = Mage::getStoreConfig('bestseller/horizontal_carousels_setting/slide_navigation');
		
		//vertical_carousels_setting
		$vertical_carousels_setting_title = Mage::getStoreConfig('bestseller/vertical_carousels_setting/title');
        $vertical_carousels_setting_limit = Mage::getStoreConfig('bestseller/vertical_carousels_setting/product_no');
        $vertical_carousels_setting_slide_itemsonpage = Mage::getStoreConfig('bestseller/vertical_carousels_setting/slide_itemsonpage');
        $vertical_carousels_setting_slide_auto = Mage::getStoreConfig('bestseller/vertical_carousels_setting/slide_auto');
        $vertical_carousels_setting_slide_navigation = Mage::getStoreConfig('bestseller/vertical_carousels_setting/slide_navigation');
		
		
        $bestsellerValues = array(
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
        return $bestsellerValues;
    }

}

?>
