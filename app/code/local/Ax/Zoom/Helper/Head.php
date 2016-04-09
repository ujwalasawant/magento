<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/Helper/Head.php
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

class Ax_Zoom_Helper_Head extends Mage_Core_Helper_Abstract
{
	public function getFancyboxCSS()
	{
		if (Mage::getStoreConfig('axzoom_options/main/ajaxZoomOpenMode') == 'fancyboxFullscreen' || Mage::getStoreConfig('axzoom_options/main/ajaxZoomOpenMode') == 'fancybox') {
			return 'axzoom/axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.3.4.css';
		}
	}
	public function getFancyboxJS()
	{
		if (Mage::getStoreConfig('axzoom_options/main/ajaxZoomOpenMode') == 'fancyboxFullscreen' || Mage::getStoreConfig('axzoom_options/main/ajaxZoomOpenMode') == 'fancybox') {
			return 'axzoom/axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.3.4.pack.js';
		}
	}
	public function getFancyboxJSAX()
	{
		if (Mage::getStoreConfig('axzoom_options/main/ajaxZoomOpenMode') == 'fancyboxFullscreen' || Mage::getStoreConfig('axzoom_options/main/ajaxZoomOpenMode') == 'fancybox') {
			return 'axzoom/axZm/extensions/jquery.axZm.openAjaxZoomInFancyBox.js';
		}
	}
	public function getColorboxCSS()
	{
		if (Mage::getStoreConfig('axzoom_options/main/ajaxZoomOpenMode') == 'colorbox') {
			return 'axzoom/axZm/plugins/demo/colorbox/example2/colorbox.css';
		}
	}
	public function getColorboxJS()
	{
		if (Mage::getStoreConfig('axzoom_options/main/ajaxZoomOpenMode') == 'colorbox') {
			return 'axzoom/axZm/plugins/demo/colorbox/jquery.colorbox-min.js';
		}
	}
}