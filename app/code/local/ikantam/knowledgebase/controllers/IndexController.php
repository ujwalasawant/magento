<?php
/**
 * Created by PhpStorm.
 * User: Ujwala
 * Date: 16-11-2015
 * Time: 18:14
 */
class ikantam_knowledgebase_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if( !Mage::getSingleton('customer/session')->isLoggedIn() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('knowledgebase/index');
        }

        $this->renderLayout();
    }
}