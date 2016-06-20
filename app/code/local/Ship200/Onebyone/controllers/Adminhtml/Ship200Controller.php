<?php
/**
 * Evirtual
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

 * @category    Evirtual Mod
 * @package     Evirtual_Autoimport
 * @author      Evirtual Core Team
 * @copyright   Copyright (c) 2012 Evirtual (http://www.evirtual.in)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ship200_Onebyone_Adminhtml_Ship200Controller extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return some checking result
     *
     * @return void
     */
    public function getmappAction()
    {
        	$this->loadLayout();
			$this->renderLayout();
    }
	
	public function savemappAction()
	{
		if ($data = $this->getRequest()->getPost()) {	
			/*$ProductAttDb=serialize($data['gui_data']['map']['product']['db']);
			$ProductAttFile=serialize($data['gui_data']['map']['product']['file']);*/
			
			$ProductAttDb=$data['gui_data']['map']['product']['db'];
			$ProductAttFile=$data['gui_data']['map']['product']['file'];
			
			$margeArray=array();
			for($i=0;$i<count($ProductAttDb);$i++){
				
				$margeArray[$ProductAttDb[$i]]=$ProductAttFile[$i];
			}
			
			/*Zend_Debug::dump($ProductAttDb);
			Zend_Debug::dump($ProductAttFile);*/
			//Zend_Debug::dump($margeArray);
			$margeArray=serialize($margeArray);
			//exit;
			
			$ConfigSwitch = new Mage_Core_Model_Config();	
			$ConfigSwitch->saveConfig('onebyone/info/shippingmapping', $margeArray, 'default', $margeArray);
			
				 Mage::getConfig()->saveConfig('onebyone/info/shippingmapping',$margeArray);

				// Refresh the config.
				Mage::app()->getStore()->resetConfig();
			
			
		}
				
	}
}