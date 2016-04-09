<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/Model/Ax360set.php
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

class Ax_Zoom_Model_Ax360set extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('axzoom/ax360set');
    }

    public function getSets($productId)
    {

		$model = Mage::getModel('axzoom/ax360set');
		$setsCollection = $model->getCollection();

		$tbl_set_group = Mage::getSingleton('core/resource')->getTableName('ajaxzoom360');
		$setsCollection->getSelect()->join(array('t2' => $tbl_set_group), 'main_table.id_360 = t2.id_360 AND t2.id_product = ' . $productId, array('t2.name', 't2.status'));
		$sets = $setsCollection->getData();

		$baseDir = Mage::getBaseDir();
		$baseUrlJs = Mage::getBaseUrl('js');
		
		foreach ($sets as &$set) {
			if (file_exists($baseDir . '/js/axzoom/pic/360/' . $productId . '/' . $set['id_360'] . '/' . $set['id_360set'])) {
				$set['path'] = $baseUrlJs . 'axzoom/axZm/zoomLoad.php?qq=1&azImg360=' . $this->rootFolder() . 'js/axzoom/pic/360/' . $productId . '/' . $set['id_360'] . '/' . $set['id_360set'] . '&width=100&height=100&thumbMode=contain';
			} else {
				$set['path'] = $baseUrlJs . 'axzoom/no_image-100x100.jpg';
			}
		}

		return $sets;
    }

    public function rootFolder()
    {
    	return preg_replace('|js/$|', '', parse_url(Mage::getBaseUrl('js'), PHP_URL_PATH));
    }
}