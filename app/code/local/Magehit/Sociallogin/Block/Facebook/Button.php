<?php

class Magehit_Sociallogin_Block_Facebook_Button extends Mage_Core_Block_Template

{

    protected $client = null;

    protected $userInfo = null;

    protected $redirectUri = null;



    protected function _construct() {

        parent::_construct();



        $this->client = Mage::getSingleton('sociallogin/facebook_client');

        if(!($this->client->isEnabled())) {

            return;

        }



        $this->userInfo = Mage::registry('magehit_sociallogin_facebook_userdetails');

        

        // CSRF protection
		
		if(!Mage::getSingleton('core/session')->getFacebookCsrf() || Mage::getSingleton('core/session')->getFacebookCsrf()=='') {
		
			$csrf = md5(uniqid(rand(), TRUE));
	
			Mage::getSingleton('core/session')->setFacebookCsrf($csrf);
		} else {
			$csrf = Mage::getSingleton('core/session')->getFacebookCsrf();
		}
			$this->client->setState($csrf);
		

        
        if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {

            $redirect = Mage::helper('core/url')->getCurrentUrl();      

        }        

        

        // Redirect uri
        Mage::getSingleton('core/session')->setFacebookRedirect($redirect);        



        $this->setTemplate('magehit/sociallogin/facebook/button.phtml');

    }



    protected function _getButtonUrl()

    {

        if(empty($this->userInfo)) {

            return $this->client->createAuthUrl();

        } else {

            return $this->getUrl('mhsociallogin/facebook/disconnect');

        }

    }



    protected function _getButtonText()

    {

        if(empty($this->userInfo)) {

            if(!($text = Mage::registry('magehit_sociallogin_button_text'))){

                $text = $this->__('Connect');

            }

        } else {

            $text = $this->__('Disconnect');

        }

        

        return $text;

    }


    protected function _getButtonClass()

    {

        if(empty($this->userInfo)) {


                $text = "mh_fb_connect";


        } else {

                $text = "mh_fb_disconnect";

        }

        

        return $text;

    }



}

