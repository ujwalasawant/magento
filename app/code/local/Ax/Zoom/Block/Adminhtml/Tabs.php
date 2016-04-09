<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/Block/Adminhtml/tabs.php
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

class Ax_Zoom_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    private $parent;
    
    protected function _prepareLayout()
    {

        $productId = Mage::app()->getRequest()->getParam('id');
        
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        
        if (!empty($productId)) {
            //add new tab
            $this->addTab(
                'tabid', array(
                'label'     => Mage::helper('catalog')->__('AJAX ZOOM'),
                'content'   => $this->getLayout()
                 ->createBlock('zoom/adminhtml_tabs_tabid')->toHtml(),
            ));
        }
        
        return $this->parent;
    }
}