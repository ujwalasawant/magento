<?php
class Magehit_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract
{
	
    public function redirect404($frontController)
    {
        $frontController->getResponse()
            ->setHeader('HTTP/1.1','404 Not Found');
        $frontController->getResponse()
            ->setHeader('Status','404 File not found');

        $pageId = Mage::getStoreConfig('web/default/cms_no_route');
        if (!Mage::helper('cms/page')->renderPage($frontController, $pageId)) {
				$frontController->_forward('defaultNoRoute');
			}
    }
    
    public function getEnabled(){
         return Mage::getStoreConfig('magehit/config/enabled', Mage::app()->getStore()->getId());
    }
    
    public function getTitle(){
         return Mage::getStoreConfig('magehit/config/title', Mage::app()->getStore()->getId());
    }
    
    public function getDefaultPosition(){
         return Mage::getStoreConfig('magehit/config/default_position', Mage::app()->getStore()->getId());
    }
}
	 