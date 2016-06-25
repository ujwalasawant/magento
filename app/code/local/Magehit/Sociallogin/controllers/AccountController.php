<?php
class Magehit_Sociallogin_AccountController extends Mage_Core_Controller_Front_Action
{
    /**
    
    * Action predispatch
    
    */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
    public function googleAction()
    {
        $userDetails = Mage::getSingleton('sociallogin/google_userdetails')->getUserDetails();
        Mage::register('magehit_sociallogin_google_userdetails', $userDetails);
        $this->loadLayout();
        $this->renderLayout();
    }
    public function facebookAction()
    {
        $userDetails = Mage::getSingleton('sociallogin/facebook_userdetails')->getUserDetails();
        Mage::register('magehit_sociallogin_facebook_userdetails', $userDetails);
        $this->loadLayout();
        $this->renderLayout();
    }
    public function twitterAction()
    {
        // Cache user info inside customer session due to Twitter window frame rate limits
        if (!($userDetails = Mage::getSingleton('customer/session')->getMhSocialLoginTwitterUserdetails())) {
            $userDetails = Mage::getSingleton('sociallogin/twitter_userdetails')->getUserDetails();
            Mage::getSingleton('customer/session')->setMhSocialLoginTwitterUserdetails($userDetails);
        }
        Mage::register('magehit_sociallogin_twitter_userdetails', $userDetails);
        $this->loadLayout();
        $this->renderLayout();
    }
    public function linkedinAction()
    {
        $userDetails = Mage::getSingleton('sociallogin/linkedin_userdetails')->getUserDetails();
        Mage::register('magehit_sociallogin_linkedin_userdetails', $userDetails);
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function instagramAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}

