<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/Block/Adminhtml/Tabs/Tabid.php
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

class Ax_Zoom_Block_Adminhtml_Tabs_Tabid extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();

		$productId = Mage::app()->getRequest()->getParam('id');
		
        if (!empty($productId)) {
	        $this->assign(array(
	        	'files' => $this->getArcList()
	        	));
	        $this->setTemplate('axzoom/tab.phtml');
    	}
    }

	public function getArcList() {
		
		$baseDir = Mage::getBaseDir();
		$files = array();
		
		if ($handle = opendir($baseDir . '/js/axzoom/zip/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' && (strtolower(substr($entry, -3)) == 'zip' || is_dir($baseDir . '/js/axzoom/zip/' . $entry)) ) {
					array_push($files, $entry);
				}
			}
			closedir($handle);
		}
		  
		return $files;
	}
}