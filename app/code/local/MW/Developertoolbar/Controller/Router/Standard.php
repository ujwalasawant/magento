<?php
class MW_Developertoolbar_Controller_Router_Standard extends Mage_Core_Controller_Varien_Router_Standard
{
     
    public function match(Zend_Controller_Request_Http $request)
    {
    	$active = Mage::getStoreConfig("dev/offline/mwdevtoolbar");
    	if($active == 1){ 
			$storeenabled = Mage::getStoreConfig('dev/offline/enable', $request->getStoreCodeFromPath());
			if ($storeenabled)
			{  
				Mage::getSingleton('core/session', array('name' => 'adminhtml'));
				$helper = Mage::helper('developertoolbar/data');
	    		if($helper->checkAllowsIps() == 0)
				{  
					Mage::getSingleton('core/session', array('name' => 'front'));
					
					$front = $this->getFront();
					$response = $front->getResponse();
				    $response->setHeader('HTTP/1.1','503 Service Temporarily Unavailable');
					$response->setHeader('Status','503 Service Temporarily Unavailable');
					$response->setHeader('Retry-After','5000');
		 
					$response->setBody(html_entity_decode( Mage::getStoreConfig('dev/offline/message', $request->getStoreCodeFromPath()), ENT_QUOTES, "utf-8" )); 			
					$response->sendHeaders();
					$response->outputBody();
					
					exit;
				}
				else
				{				
					$showreminder = Mage::getStoreConfig('dev/offline/reminder', $request->getStoreCodeFromPath());
					if ($showreminder)
					{
						$front = $this->getFront();
						$response = $front->getResponse()->clearBody();
						$response = $front->getResponse()->appendBody('<div style="height:12px; background:red; color: white; position:relative; width:100%;padding:3px; z-index:100000;text-trasform:capitalize;">Offline</div>');
					}
				}
			}
    	}
		return parent::match($request);
        
    }  
}