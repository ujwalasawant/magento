<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/Model/Observer.php
*  Copyright: Copyright (c) 2010-2015 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.0.0
*  Date: 2015-09-08
*  Review: 2015-09-08
*  URL: http://www.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=modules&module=magento
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2015 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/

class Ax_Zoom_Model_Observer {
    public function coreBlockBefore($observer)
    {
        
        if ($observer->getBlock() instanceof Mage_Catalog_Block_Product_View_Media) {

            // if AJAX ZOOM is enabled for exact product then replace the product/view/media block
            $productId = Mage::app()->getRequest()->getParam('id');
            
            $ax = Mage::getModel('axzoom/ax360');
            $active = $ax->isProductActive($productId);

            if($active && $ax->isOnlyProductActive($productId)) {
                $observer->getBlock()->setTemplate('ax_zoom/catalog/product/view/media.phtml');
            }
        }
    }


    public function productBefore($observer)
    {
    }

    public function productSaveBefore($observer)
    {
    }

    public function productSaveAfter($observer)
    {
    }    

    public function deleteProduct($observer)
    {
        $product = $observer->getProduct();
        $model = Mage::getModel('axzoom/ax360');
        $sets = Mage::getModel('axzoom/ax360set')->getSets($product->entity_id);

        // clear AZ cache
        foreach ($sets as $set) {
            $images = $model->get360Images($product->entity_id, $set['id_360set']);

            foreach ($images as $image) {
                $model->deleteImageAZcache($image['filename']);
            }
        }

        if ($images = $product->getMediaGalleryImages()) foreach ($images as $image) {
            $model->deleteImageAZcache(basename($image->getFile()));
        }
    }

    public function save360($observer)
    {
        $productId = $observer->product->entity_id;
    	$postData = Mage::app()->getRequest()->getPost();
        
        // remove images from Ax cache if image checked as remove
        $images = Mage::helper('core')->jsonDecode($postData['product']['media_gallery']['images']);
        foreach ($images as $image) {
            if(isset($image['removed']) && $image['removed'] == 1) {
                Mage::getModel('axzoom/ax360')->deleteImageAZcache(basename($image['file']));
            }
        }
    	
    	// save status 
    	if (isset($postData['az_active']) && $postData['az_active'] == 1) {
    		$this->activateAx($productId);
    	} else {
    		Mage::getModel('axzoom/axproducts')->setData(array('id_product' => $productId))->save();
    	}

    	// save settings
    	if(isset($postData['settings'])) foreach ($postData['settings'] as $id_360 => $string) {

    		Mage::getModel('axzoom/ax360')->load($id_360)->addData(array(
                'settings' => urldecode($string),
                'combinations' => urldecode($postData['comb'][$id_360])
                ))->setId($id_360)->save();
    	}
    }
    
    public function activateAx($productId)
    {
		$res = Mage::getSingleton('core/resource');
		$con = $res->getConnection('core_write');
		$table = $res->getTableName('axzoom/table_axproducts');
		$query = "DELETE FROM {$table} WHERE id_product = " . (int)$productId;
		$con->query($query);
    }
}