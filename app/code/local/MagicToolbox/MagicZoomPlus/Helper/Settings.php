<?php

class MagicToolbox_MagicZoomPlus_Helper_Settings extends Mage_Core_Helper_Abstract {

    static private $_toolCoreClass = null;
    static private $_scrollCoreClass = null;
    static private $_templates = array();
    private $_default_templates = array();
    private $_interface;
    private $_theme;
    //private $_skin;

    public function __construct() {

        $designPackage = Mage::getSingleton('core/design_package');
        $this->_interface = $designPackage->getPackageName();
        $this->_theme = $designPackage->getTheme('template');
        //$this->_skin = $designPackage->getTheme('skin');
        $this->_default_templates = array(
            'product.info.media' => 'catalog'.DS.'product'.DS.'view'.DS.'media.phtml',
            'product_list' => 'catalog'.DS.'product'.DS.'list.phtml',
            'search_result_list' => 'catalog'.DS.'product'.DS.'list.phtml',
            'right.reports.product.viewed' => 'reports'.DS.'product_viewed.phtml',
            'left.reports.product.viewed' => 'reports'.DS.'product_viewed.phtml',
            'home.catalog.product.new' => 'catalog'.DS.'product'.DS.'new.phtml',
        );

    }

    public function getBlockTemplate($blockName, $template) {
        //NOTE: to save original template
        if(!isset(self::$_templates[$blockName])) {
            $block = Mage::app()->getLayout()->getBlock($blockName);
            if($block) {
                self::$_templates[$blockName] = $block->getTemplate();
            }
        }
        return $template;
    }

    public function setOriginalTemplate($blockName, $template) {
        self::$_templates[$blockName] = $template;
    }

    public function getOriginalTemplate($blockName, $default = '') {
        return isset(self::$_templates[$blockName]) ? self::$_templates[$blockName] : $default;
    }

    public function getTemplateFilename($blockName, $defaultTemplate = '') {
        $template = isset(self::$_templates[$blockName]) ? self::$_templates[$blockName] :
                    (isset($this->_default_templates[$blockName]) ? $this->_default_templates[$blockName] :
                    $defaultTemplate);
        return Mage::getSingleton('core/design_package')->getTemplateFilename($template);
    }

    public function loadTool($_profile = '') {

        if(null === self::$_toolCoreClass) {

            $helper = Mage::helper('magiczoomplus/params');

            require_once(BP . str_replace('/', DS, '/app/code/local/MagicToolbox/MagicZoomPlus/core/magiczoomplus.module.core.class.php'));
            self::$_toolCoreClass = new MagicZoomPlusModuleCoreClass();

            /*
            foreach($helper->getDefaultValues() as $block => $params) {
                foreach($params as $id => $value) {
                    self::$_toolCoreClass->params->setValue($id, $value, $block);
                }
            }
            */

            $store = Mage::app()->getStore();
            $website_id = $store->getWebsiteId();
            $group_id = $store->getGroupId();
            $store_id = $store->getId();

            $designPackage = Mage::getSingleton('core/design_package');
            $interface = $designPackage->getPackageName();
            $theme = $designPackage->getTheme('template');


            $model = Mage::getModel('magiczoomplus/settings');
            $collection = $model->getCollection();
            $zendDbSelect = $collection->getSelect();
            $zendDbSelect->where('(website_id = ?) OR (website_id IS NULL)', $website_id);
            $zendDbSelect->where('(group_id = ?) OR (group_id IS NULL)', $group_id);
            $zendDbSelect->where('(store_id = ?) OR (store_id IS NULL)', $store_id);
            $zendDbSelect->where('(package = ?) OR (package = \'\')', $interface);
            $zendDbSelect->where('(theme = ?) OR (theme = \'\')', $theme);
            $zendDbSelect->order(array('theme DESC', 'package DESC', 'store_id DESC', 'group_id DESC', 'website_id DESC'));

            $settings = $collection->getFirstItem()->getValue();
            if(!empty($settings)) {
                $settings = unserialize($settings);
                if(isset($settings['desktop'])) {
                    foreach($settings['desktop'] as $profile => $params) {
                        foreach($params  as $id => $value) {
                            self::$_toolCoreClass->params->setValue($id, $value, $profile);
                        }
                    }
                }
                if(isset($settings['mobile'])) {
                    foreach($settings['mobile'] as $profile => $params) {
                        foreach($params  as $id => $value) {
                            self::$_toolCoreClass->params->setMobileValue($id, $value, $profile);
                        }
                    }
                }
            }

            foreach($helper->getProfiles() as $id => $label) {

                /* load locale */
                $locale = $this->__('MagicZoomPlus_Message');
                if($locale != 'MagicZoomPlus_Message') {
                    self::$_toolCoreClass->params->setValue('message', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_ZoomHintTextOnHover');
                if($locale != 'MagicZoomPlus_ZoomHintTextOnHover') {
                    self::$_toolCoreClass->params->setValue('textHoverZoomHint', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_ZoomHintTextOnClick');
                if($locale != 'MagicZoomPlus_ZoomHintTextOnClick') {
                    self::$_toolCoreClass->params->setValue('textClickZoomHint', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_ZoomHintTextOnHoverForMobile');
                if($locale != 'MagicZoomPlus_ZoomHintTextOnHoverForMobile') {
                    self::$_toolCoreClass->params->setValue('textHoverZoomHintForMobile', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_ZoomHintTextOnClickForMobile');
                if($locale != 'MagicZoomPlus_ZoomHintTextOnClickForMobile') {
                    self::$_toolCoreClass->params->setValue('textClickZoomHintForMobile', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_ExpandHintText');
                if($locale != 'MagicZoomPlus_ExpandHintText') {
                    self::$_toolCoreClass->params->setValue('textExpandHint', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_ExpandHintTextForMobile');
                if($locale != 'MagicZoomPlus_ExpandHintTextForMobile') {
                    self::$_toolCoreClass->params->setValue('textExpandHintForMobile', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_CloseButtonText');
                if($locale != 'MagicZoomPlus_CloseButtonText') {
                    self::$_toolCoreClass->params->setValue('textBtnClose', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_NextButtonText');
                if($locale != 'MagicZoomPlus_NextButtonText') {
                    self::$_toolCoreClass->params->setValue('textBtnNext', $locale, $id);
                }
                $locale = $this->__('MagicZoomPlus_PrevButtonText');
                if($locale != 'MagicZoomPlus_PrevButtonText') {
                    self::$_toolCoreClass->params->setValue('textBtnPrev', $locale, $id);
                }
            }


            $loadScroll = false;
            $layout = Mage::app()->getLayout();//Mage_Core_Model_Layout
            if($layout) {
                $headerBlock = $layout->getBlock('magiczoomplus_header');//MagicToolbox_MagicZoomPlus_Block_Header
                if($headerBlock) {
                    $loadScroll = ($headerBlock->getPageType() == 'product');
                }

            }
            if($loadScroll && self::$_toolCoreClass->params->checkValue('magicscroll', 'yes', 'product')) {
                require_once(BP . str_replace('/', DS, '/app/code/local/MagicToolbox/MagicZoomPlus/core/magicscroll.module.core.class.php'));
                self::$_scrollCoreClass = new MagicScrollModuleCoreClass(false);
                //NOTE: load params in a separate profile, in order not to overwrite the options of MagicScroll module
                self::$_scrollCoreClass->params->appendParams(self::$_toolCoreClass->params->getParams('product'), 'product-magicscroll-options');
                self::$_scrollCoreClass->params->setValue('orientation', (self::$_toolCoreClass->params->checkValue('template', array('left', 'right'), 'product') ? 'vertical' : 'horizontal'), 'product-magicscroll-options');
                //NOTE: if Magic Scroll module installed we need to load settings before displaying custom options
                if(Mage::getConfig()->getHelperClassName('magicscroll/settings') == 'MagicToolbox_MagicScroll_Helper_Settings') {
                    $magicscrollHelper = Mage::helper('magicscroll/settings');
                    $magicscrollHelper->loadTool();
                }

            }
            require_once(BP . str_replace('/', DS, '/app/code/local/MagicToolbox/MagicZoomPlus/core/magictoolbox.templatehelper.class.php'));
            //MagicToolboxTemplateHelperClass::setPath(dirname(Mage::getSingleton('core/design_package')->getTemplateFilename('magiczoomplus'.DS.'media.phtml')) . DS . 'templates');
            MagicToolboxTemplateHelperClass::setPath(
                dirname(
                    Mage::getSingleton('core/design_package')->getTemplateFilename(
                        'magiczoomplus'.DS.'templates'.DS.preg_replace('/[^a-zA-Z0-9_]/is', '-', self::$_toolCoreClass->params->getValue('template', 'product')).'.tpl.php'
                    )
                )
            );
            MagicToolboxTemplateHelperClass::setOptions(self::$_toolCoreClass->params);
        }

        if($_profile) {
            self::$_toolCoreClass->params->setProfile($_profile);
        }

        return self::$_toolCoreClass;
    }

    public function loadScroll() {
        return self::$_scrollCoreClass;
    }

    public function magicToolboxGetSizes($sizeType, $originalSizes = null) {

        $w = self::$_toolCoreClass->params->getValue($sizeType.'-max-width');
        $h = self::$_toolCoreClass->params->getValue($sizeType.'-max-height');
        if(empty($w)) $w = 0;
        if(empty($h)) $h = 0;
        if(self::$_toolCoreClass->params->checkValue('square-images', 'No')) {
            //NOTE: fix for bad images
            if(empty($originalSizes[0]) || empty($originalSizes[1])) {
                return array($w, $h);
            }
            list($w, $h) = self::calculateSize($originalSizes[0], $originalSizes[1], $w, $h);
        } else {
            $h = $w = $h ? ($w ? min($w, $h) : $h) : $w;
        }
        return array($w, $h);
    }

    private function calculateSize($originalW, $originalH, $maxW = 0, $maxH = 0) {
        if(!$maxW && !$maxH) {
            return array($originalW, $originalH);
        } elseif(!$maxW) {
            $maxW = ($maxH * $originalW) / $originalH;
        } elseif(!$maxH) {
            $maxH = ($maxW * $originalH) / $originalW;
        }
        $sizeDepends = $originalW/$originalH;
        $placeHolderDepends = $maxW/$maxH;
        if($sizeDepends > $placeHolderDepends) {
            $newW = $maxW;
            $newH = $originalH * ($maxW / $originalW);
        } else {
            $newW = $originalW * ($maxH / $originalH);
            $newH = $maxH;
        }
        return array(round($newW), round($newH));
    }

    public function isModuleOutputEnabled($moduleName = null) {

        if($moduleName === null) {
            $moduleName = 'MagicToolbox_MagicZoomPlus';//$this->_getModuleName();
        }
        if(method_exists('Mage_Core_Helper_Abstract', 'isModuleOutputEnabled')) {
            return parent::isModuleOutputEnabled($moduleName);
        }
        //if (!$this->isModuleEnabled($moduleName)) {
        //    return false;
        //}
        if(Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName)) {
            return false;
        }
        return true;
    }

}
