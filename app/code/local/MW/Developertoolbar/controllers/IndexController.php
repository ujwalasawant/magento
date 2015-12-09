<?php
class MW_Developertoolbar_IndexController extends Mage_Core_Controller_Front_Action
{
	public function storeofflineAction(){
    	$active = Mage::getStoreConfig("dev/offline/mwdevtoolbar");
    	if($active == 1){
			$enabled = $this->getRequest()->getParam('enabled');	
			$scope = 'stores';
			$scope_id = Mage::app()->getStore()->getStoreId();
			
			Mage::getConfig()->saveConfig('dev/offline/enable', $enabled, $scope, $scope_id);
			Mage::getConfig()->saveConfig('dev/offline/reminder', $enabled, $scope, $scope_id);			
	    	Mage::app()->cleanCache();
    	}
		$this->_redirectReferer();
	}
	
	public function hintsAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$enabled = $this->getRequest()->getParam('enabled');
			$type = $this->getRequest()->getParam('type');			
			$scope = $type === 'front' ? 'stores' : 'default';
			$scope_id = $type === 'front' ? Mage::app()->getStore()->getStoreId() : '0';
			Mage::getConfig()->saveConfig('dev/debug/template_hints', $enabled, $scope, $scope_id);
			Mage::getConfig()->saveConfig('dev/debug/template_hints_blocks', $enabled, $scope, $scope_id);
    		Mage::app()->cleanCache();
    	}
    	$this->_redirectReferer();
    }
	
	public function logAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$scope = 'stores';
			$scope_id = Mage::app()->getStore()->getStoreId();
			$enabled = $this->getRequest()->getParam('enabled');
			Mage::getConfig()->saveConfig('dev/log/active', $enabled, $scope, $scope_id);
		}
    	$this->_redirectReferer();
    }
	
	public function jsAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$scope = 'stores';
			$scope_id = Mage::app()->getStore()->getStoreId();
			$enabled = $this->getRequest()->getParam('enabled');	
			Mage::getConfig()->saveConfig('dev/js/merge_files', $enabled, $scope, $scope_id);
			Mage::getModel('core/design_package')->cleanMergedJsCss();
		}
    	$this->_redirectReferer();
    }
	
	public function urlAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$enabled = $this->getRequest()->getParam('enabled');
			Mage::getConfig()->saveConfig('web/url/use_store', $enabled);
			Mage::app()->cleanCache();
		}
    	$this->_redirectReferer();
    }
	
	public function seoAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$enabled = $this->getRequest()->getParam('enabled');	
			Mage::getConfig()->saveConfig('web/seo/use_rewrites', $enabled);
			Mage::app()->cleanCache();
		}
    	$this->_redirectReferer();
	}
	
	public function translateAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$scope = 'stores';
			$scope_id = Mage::app()->getStore()->getStoreId();
			$enabled = $this->getRequest()->getParam('enabled');	
			Mage::getConfig()->saveConfig('dev/translate_inline/active', $enabled, $scope, $scope_id);
			
    		Mage::app()->cleanCache();
		}
    	$this->_redirectReferer();
    }
    
	public function cssAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$scope = 'stores';
			$scope_id = Mage::app()->getStore()->getStoreId();
			$enabled = $this->getRequest()->getParam('enabled');	
			Mage::getConfig()->saveConfig('dev/css/merge_css_files', $enabled, $scope, $scope_id);
			Mage::getModel('core/design_package')->cleanMergedJsCss();
		}
    	$this->_redirectReferer();
    }
    
	public function profilerAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$scope = 'stores';
			$scope_id = Mage::app()->getStore()->getStoreId();
			$enabled = $this->getRequest()->getParam('enabled');	
			Mage::getConfig()->saveConfig('dev/debug/profiler', $enabled, $scope, $scope_id);
			
    		Mage::app()->cleanCache();
		}
    	$this->_redirectReferer();
    }
    
	public function symlinkAction()
    {        
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			$scope = 'stores';
			$scope_id = Mage::app()->getStore()->getStoreId();
			$enabled = $this->getRequest()->getParam('enabled');	
			Mage::getConfig()->saveConfig('dev/template/allow_symlink', $enabled, $scope, $scope_id);
		}
    	$this->_redirectReferer();
    }
	
	public function encacheAction()
    {       
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			Mage::app()->cleanCache();
			$cacheTypes = array_keys(Mage::helper('core')->getCacheTypes());	
			//Zend_debug::dump($cacheTypes);die;
			$enable = array();
	        foreach ($cacheTypes as $type) {            
	            $enable[$type] = 1;            		
	        }
	      
			Mage::app()->saveUseCache($enable);				
		}
    	$this->_redirectReferer();
    }
    
	public function discacheAction()
    {       
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			Mage::app()->cleanCache();
			$cacheTypes = array_keys(Mage::helper('core')->getCacheTypes());
			$enable = array();
	        foreach ($cacheTypes as $type) {            
	            $enable[$type] = 0;            		
	        }
			Mage::app()->saveUseCache($enable);		
    	}		
		$this->_redirectReferer();
    }
    
	public function refreshAction()
    {       
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
			Mage::app()->cleanCache();
			$cacheTypes = array_keys(Mage::helper('core')->getCacheTypes());	
			//Zend_debug::dump($cacheTypes);die;
			$enable = array();
	        foreach ($cacheTypes as $type) {        
	            $enable[$type] = 1;            		
	        }
			Mage::app()->saveUseCache($enable);				
		}
    	$this->_redirectReferer();
    }
    
    public function flushcachedataAction()
    {  
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
	    	$types = Mage::app()->getCacheInstance()->getTypes();
		    //flush();
		    foreach ($types as $type => $data) {
		        Mage::app()->getCacheInstance()->clean($data["tags"]);
		    }
	    }
    	$this->_redirectReferer();
    }
    
	public function flushcachestorageAction()
    {  
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
		    //flush();
		    Mage::app()->getCacheInstance()->clean();
	    }
    	$this->_redirectReferer();
    }
    
	public function flushcachejsAction()
    {  
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
		    //flush();
		   	Mage::getModel('core/design_package')->cleanMergedJsCss();
		}
    	$this->_redirectReferer();
    }
    
	public function flushcacheimageAction()
    {  
    	$helper = Mage::helper('developertoolbar/data');
    	if($helper->checkAllowsIps() > 0){
		    //flush();
		    Mage::getModel('catalog/product_image')->clearCache();
		}
    	$this->_redirectReferer();
    }
}