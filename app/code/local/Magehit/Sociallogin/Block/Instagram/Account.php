<?php
class Magehit_Sociallogin_Block_Instagram_Account extends Mage_Core_Block_Template
{
    protected $client = null;
    protected $userID = null;
    protected function _construct()
    {
        parent::_construct();
        $this->client = Mage::getSingleton('sociallogin/instagram_client');
        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $this->userID = Mage::getSingleton('customer/session')->getCustomer()->getData('magehit_sociallogin_igid');
        }
        if (!($this->client->isEnabled())) {
            return;
        }
        $this->setTemplate('magehit/sociallogin/instagram/account.phtml');
    }
   
    protected function _getInstagramId()
    {
        return $this->userID;
    }
    protected function _getWebsite()
    {
        $link = "";
        if (Mage::getSingleton('customer/session')->getDataWebsite()!="") {
            $link = '<a style="background-image:none !important" href="' . Mage::getSingleton('customer/session')->getDataWebsite() . '" target="_blank">' . $this->htmlEscape(Mage::getSingleton('customer/session')->getDataWebsite()) . '</a>';
        } 
        return $link;
    }    
    protected function _getUsername()
    {
        return Mage::getSingleton('customer/session')->getDataUsername();
    }
    protected function _getPicture()
    {
        if (Mage::getSingleton('customer/session')->getDataProfilePicture()!="") {
            return Mage::helper('sociallogin/instagram')->getProperDimensionsPictureUrl($this->_getInstagramId(), Mage::getSingleton('customer/session')->getDataProfilePicture());
        }
        return null;
    }
    protected function _getFullName()
    {
        return Mage::getSingleton('customer/session')->getDataFullName();
    }
}