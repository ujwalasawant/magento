<?php
class Magehit_Sociallogin_Block_Login extends Mage_Core_Block_Template
{
    protected $clientGoogle = null;
    protected $clientFacebook = null;
    protected $clientTwitter = null;
    protected $clientLinkedin = null;
    protected $clientInstagram = null;
    protected function _construct() {
        parent::_construct();

        $this->clientGoogle = Mage::getSingleton('sociallogin/google_client');
        $this->clientFacebook = Mage::getSingleton('sociallogin/facebook_client');
        $this->clientTwitter = Mage::getSingleton('sociallogin/twitter_client');
        $this->clientLinkedin = Mage::getSingleton('sociallogin/linkedin_client');
        $this->clientInstagram = Mage::getSingleton('sociallogin/instagram_client');
        $sessionCustomer = Mage::getSingleton("customer/session");
        if($sessionCustomer->isLoggedIn()){
            return;
        }
	    if( !$this->_googleEnabled() &&
            !$this->_facebookEnabled() &&
            !$this->_twitterEnabled() && 
            !$this->_linkedinEnabled() &&
            !$this->_instagramEnabled())
            return;

        $this->setTemplate('magehit/sociallogin/login.phtml');
    }
    protected function _googleEnabled()
    {
        return $this->clientGoogle->isEnabled();
    }

    protected function _facebookEnabled()
    {
        return $this->clientFacebook->isEnabled();
    }

    protected function _twitterEnabled()
    {
        return $this->clientTwitter->isEnabled();
    }

    protected function _linkedinEnabled()
    {
        return $this->clientLinkedin->isEnabled();
    }
    
    protected function _instagramEnabled()
    {
        return $this->clientInstagram->isEnabled();
    }

}
