<?php

class Magehit_Sociallogin_Block_Instagram_Button extends Mage_Core_Block_Template

{

    protected $client = null;

    protected $userInfo = null;

    protected $redirectUri = null;



    protected function _construct() {

        parent::_construct();



        $this->client = Mage::getSingleton('sociallogin/instagram_client');

        if(!($this->client->isEnabled())) {

            return;

        }



        $this->userInfo = Mage::registry('magehit_sociallogin_instagram_userdetails');

        

        // CSRF protection
		
		if(!Mage::getSingleton('core/session')->getInstagramCsrf() || Mage::getSingleton('core/session')->getInstagramCsrf()=='') {
		
			$csrf = md5(uniqid(rand(), TRUE));
	
			Mage::getSingleton('core/session')->setInstagramCsrf($csrf);
		} else {
			$csrf = Mage::getSingleton('core/session')->getInstagramCsrf();
		}
			$this->client->setState($csrf);
		

        
        if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {

            $redirect = Mage::helper('core/url')->getCurrentUrl();      

        }        

        

        // Redirect uri
        Mage::getSingleton('core/session')->setInstagramRedirect($redirect);        



        $this->setTemplate('magehit/sociallogin/instagram/button.phtml');

    }



    protected function _getButtonUrl()

    {

        if(empty($this->userInfo)) {

            return $this->client->createAuthUrl();

        } else {

            return $this->getUrl('mhsociallogin/instagram/disconnect');

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


                $text = "mh_ig_connect";


        } else {

                $text = "mh_ig_disconnect";

        }

        

        return $text;

    }



}

