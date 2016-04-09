<?php
/**
*  Module: jQuery AJAX-ZOOM for Magento, /app/code/local/Ax/Zoom/Model/Position.php
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

class Ax_Zoom_Model_Position
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'inside', 'label'=>Mage::helper('axzoom')->__('inside')),
            array('value'=>'top', 'label'=>Mage::helper('axzoom')->__('top')),
            array('value'=>'right', 'label'=>Mage::helper('axzoom')->__('right')),
            array('value'=>'bottom', 'label'=>Mage::helper('axzoom')->__('bottom')),
            array('value'=>'left', 'label'=>Mage::helper('axzoom')->__('left'))
        );
    }
}