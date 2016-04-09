<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/Model/Ax360.php
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

class Ax_Zoom_Model_Ax360 extends Mage_Core_Model_Abstract
{
	static $axZmH;
	static $zoom;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('axzoom/ax360');
    }

    public function getSetsGroups($productId)
    {
    	$tblItems = Mage::getSingleton('core/resource')->getTableName('ajaxzoom360set');
		$collection = Mage::getModel('axzoom/ax360')->getCollection();
		$collection->getSelect()->join(array('i' => $tblItems),
			'main_table.id_360 = i.id_360',
			array('qty' => 'COUNT(i.id_360)', 'id_360set' => 'id_360set'));

		$collection->getSelect()->group('main_table.id_360');
		return $collection->addFieldToFilter('id_product', $productId)->getData();
    }

    public function getSetsGroup($id360)
    {
    	$tblItems = Mage::getSingleton('core/resource')->getTableName('ajaxzoom360set');
		$collection = Mage::getModel('axzoom/ax360')->getCollection();
		$collection->getSelect()->join(array('i' => $tblItems),
			'main_table.id_360 = i.id_360',
			array('qty' => 'COUNT(i.id_360)', 'id_360set' => 'id_360set'));

		$collection->getSelect()->group('main_table.id_360');
		return $collection->addFieldToFilter('main_table.id_360', $id360)->getData();
    }

	public function images360Json($productId, $extraGroups = array())
	{
		
		$extraGroups = array_unique($extraGroups);
		
		$json = '{';
		$cnt = 1;

		if (!is_array($productId))	{
			$products = array($productId);
		} else {
			$products = $productId;
		}

		foreach ($products as $productId) {
			$setsGroups = $this->getSetsGroups($productId);
			foreach ($setsGroups as $group) {

				if ($group['status'] == 0)
					continue;

				$settings = $this->prepareSettings($group['settings']);
				if (!empty($settings)) $settings = ", $settings";

				if ($group['qty'] > 0) {
					if ($group['qty'] == 1) {
						$json .= "'" . $group['id_360'] . "'" . ":  {'path': '" . $this->rootFolder() . "js/axzoom/pic/360/" . $productId . "/" . $group['id_360'] . "/" . $group['id_360set'] . "'" . $settings . ", 'combinations': [" . $group['combinations'] . "]}";
					} else {
						$json .= "'" . $group['id_360'] . "'" . ":  {'path': '" . $this->rootFolder() . "js/axzoom/pic/360/" . $productId . "/" . $group['id_360'] . "'" . $settings . ", 'combinations': [" . $group['combinations'] . "]}";
					}
					$cnt++;
					if ($cnt != count($setsGroups)+1) $json .= ',';
				}
			}
		}

		$cnt = 1;
		if ($extraGroups) foreach ($extraGroups as $id360) {
			
			$setsGroup = $this->getSetsGroup($id360);
			$group = $setsGroup[0];

			if ($group['status'] == 0)
				continue;

			$settings = $this->prepareSettings($group['settings']);
			if (!empty($settings)) $settings = ", $settings";

			if ($group['qty'] > 0) {
				if ($group['qty'] == 1) {
					$json .= "'" . $group['id_360'] . "'" . ":  {'path': '" . $this->rootFolder() . "js/axzoom/pic/360/" . $group['id_product'] . "/" . $group['id_360'] . "/" . $group['id_360set'] . "'" . $settings . ", 'combinations': [" . $group['combinations'] . "]}";
				} else {
					$json .= "'" . $group['id_360'] . "'" . ":  {'path': '" . $this->rootFolder() . "js/axzoom/pic/360/" . $group['id_product'] . "/" . $group['id_360'] . "'" . $settings . ", 'combinations': [" . $group['combinations'] . "]}";
				}
				$cnt++;
				if ($cnt != count($extraGroups)+1) $json .= ',';
			}
			  
		}
		
		$json .= '}';

		return $json;
	}

	public function prepareSettings($str)
	{
		$res = array();
		$settings = (array)Mage::helper('core')->jsonDecode($str);
		foreach ($settings as $key => $value) {
			if ($value == 'false' || $value == 'true' || $value == 'null' || is_numeric($value) ||  substr($value, 0, 1) == '{' ||  substr($value, 0, 1) == '[') {
				$res[] = "'$key': $value";
			} else {
				$res[] = "'$key': '$value'";
			}
		}
		return implode(', ', $res);
	}

	public function get360Images($productId, $id360set = '')
	{
		$files = array();
		$id360 = Mage::getModel('axzoom/ax360set')->load($id360set)->getId_360();

		$dir = Mage::getBaseDir() . '/js/axzoom/pic/360/' . $productId . '/' . $id360 . '/' . $id360set;
		if (file_exists($dir) && $handle = opendir($dir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$files[] = $entry;
				}
			}
			closedir($handle);
		}
		sort($files);

		$res = array();
		foreach ($files as $entry) {
			$tmp = explode('.', $entry);
			$ext = end($tmp);
			$name = preg_replace('|\.' . $ext . '$|', '', $entry);
			$res[] = array(
				'thumb' => Mage::getBaseUrl('js') . 'axzoom/axZm/zoomLoad.php?azImg=' . $this->rootFolder() . 'js/axzoom/pic/360/' . $productId . '/' . $id360 . '/' . $id360set . '/' . $entry . '&width=100&height=100&qual=90',
				'filename' => $entry,
				'id' => $name,
				'ext' => $ext
				); 
		}

		return $res;
	}

	public function deleteImageAZcache($file)
	{ 

		// Include all classes
		include_once (Mage::getBaseDir() . '/js/axzoom/axZm/zoomInc.inc.php');
		//error_reporting(E_ALL);
		if (!Ax_Zoom_Model_Ax360::$axZmH){
			Ax_Zoom_Model_Ax360::$axZmH = $axZmH;
			Ax_Zoom_Model_Ax360::$zoom = $zoom;
		}
		
		// What to delete
		$arrDel = array('In' => true, 'Th' => true, 'tC' => true, 'mO' => true, 'Ti' => true);

		// Remove all cache
		Ax_Zoom_Model_Ax360::$axZmH->removeAxZm(Ax_Zoom_Model_Ax360::$zoom, $file, $arrDel, false);
	}

    public function rootFolder()
    {
        return preg_replace('|js/$|', '', parse_url(Mage::getBaseUrl('js'), PHP_URL_PATH));
    }

	public function isProductActive($productId)
	{
		return !Mage::getModel('axzoom/axproducts')->getCollection()->addFieldToFilter('id_product', $productId)->count();
	}

	public function getCSV($input, $delimiter = ",", $enclosure = '"', $escape = "\\")
	{
		if (function_exists('str_getcsv')) {
			return str_getcsv($input, $delimiter, $enclosure, $escape);
		}
		else {
			$temp = fopen('php://memory', 'rw');
			fwrite($temp, $input);
			fseek($temp, 0);
			$r = fgetcsv($temp, 0, $delimiter, $enclosure);
			fclose($temp);
			return $r;
		}
	}
	
	public function isOnlyProductActive($productId)
	{
				
		$products = Mage::getStoreConfig('axzoom_options/products/displayOnlyForThisProductID');
		
		if (empty($products)) {
			return true;
		}
		
		$arr = $this->getCSV($products);
		if (in_array($productId, $arr)) {
			return true;
		}
		return false;
	}
	
	public function imagesJsonAll($arr)
	{
		$imagesJson = array();
		foreach ($arr as $k=>$v){
			array_push($imagesJson, '{img: "' .$v . '", title: ""}');
		}
		return '[' .implode(', ', $imagesJson). ']';
	}
	
	public function findDefaultLabelValue($arr, $key)
	{
		if (!is_array($arr)){return false;}
		foreach ($arr as $k=>$v){
			if (isset($v['value']) && $v['value'] == $key && isset($v['label'])){
				return $v['label'];
			}
		}
		return false;
	}
}

Ax_Zoom_Model_Ax360::$axZmH;
Ax_Zoom_Model_Ax360::$zoom;