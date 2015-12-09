<?php
/**
 * Morningtime Extensions
 * http://shop.morningtime.com
 *
 * @extension   FancyZoom
 * @type        Simple product media lightbox
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Morningtime
 * @package     Morningtime_FancyZoom
 * @copyright   Copyright (c) 2011-2012 Morningtime Internetbureau B.V. (http://www.morningtime.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Morningtime_FancyZoom_Block_Media extends Mage_Catalog_Block_Product_View_Media
{
    public $_storeId;
    
    public function _construct() {
        parent::_construct();
        $this->_store_id = Mage::app()->getStore()->getStoreId();
    }

    /**
     * Return Morningtime_FancyZoom_Model_Media
     */
    public function getFancyData($value)
    {
        return Mage::getSingleton('fancyzoom/media')->getConfigData('fancyzoom/settings/' . $value, $this->_storeId);
    }

    public function getImageWidth()
    {
        return intval($this->getFancyData('image_width'));
    }
    
    public function getImageHeight()
    {
        return intval($this->getFancyData('image_height'));
    }
    
    public function getThumbWidth()
    {
        return intval($this->getFancyData('thumb_width'));
    }
    
    public function getThumbHeight()
    {
        return intval($this->getFancyData('thumb_height'));
    }
    
    public function getFancyWidth()
    {
        return intval($this->getFancyData('fancy_width'));
    }
    
    public function getFancyHeight()
    {
        return intval($this->getFancyData('fancy_height'));
    }
    
    public function getShowGallery()
    {
        return (bool) $this->getFancyData('show_gallery');
    }
    
    public function getMainZoom()
    {
        return (bool) $this->getFancyData('main_zoom');
    }
    
    public function getGalleryItems()
    {
        $default = Morningtime_FancyZoom_Model_Media::FANCYZOOM_MINIMUM_ITEMS;
        $items = intval($this->getFancyData('gallery_items'));
        
        return ($items > $default) ? $items : $default;
    }
    
    public function getGalleryMargin()
    {
        return Morningtime_FancyZoom_Model_Media::FANCYZOOM_GALLERY_MARGIN;
    }

}
