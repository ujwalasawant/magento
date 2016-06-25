<?php

class Magehit_Sociallogin_Block_Linkedin_Button extends Mage_Core_Block_Template

{

    protected $client = null;

    protected $userInfo = null;

    protected $redirectUri = null;



    protected function _construct() {

        parent::_construct();



        $this->client = Mage::getSingleton('sociallogin/linkedin_client');

        if(!($this->client->isEnabled())) {

            return;

        }



        $this->userInfo = Mage::registry('magehit_sociallogin_linkedin_userdetails');

        

        // CSRF protection

		if(!Mage::getSingleton('core/session')->getLinkedinCsrf() || Mage::getSingleton('core/session')->getLinkedinCsrf()=='') {
			$csrf = md5(uniqid(rand(), TRUE));	
			Mage::getSingleton('core/session')->setLinkedinCsrf($csrf);
		} else {
			$csrf = Mage::getSingleton('core/session')->getLinkedinCsrf();
		}
		$this->client->setState($csrf);

        if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {

            $redirect = Mage::helper('core/url')->getCurrentUrl();      

        }        


        // Redirect uri

        Mage::getSingleton('core/session')->setLinkedinRedirect($redirect);        



        $this->setTemplate('magehit/sociallogin/linkedin/button.phtml');

    }



    protected function _getButtonUrl()

    {

        if(empty($this->userInfo)) {

            return $this->client->createAuthUrl();

        } else {

            return $this->getUrl('mhsociallogin/linkedin/disconnect');

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


                $text = "mh_linkedin_connect";


        } else {

                $text = "mh_linkedin_disconnect";

        }

        

        return $text;

    }



}

