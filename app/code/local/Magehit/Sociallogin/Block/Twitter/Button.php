<?php
class Magehit_Sociallogin_Block_Twitter_Button extends Mage_Core_Block_Template

{

    protected $client = null;

    protected $userInfo = null;



    protected function _construct() {

        parent::_construct();



        $this->client = Mage::getSingleton('sociallogin/twitter_client');

        if(!($this->client->isEnabled())) {

            return;

        }



        $this->userInfo = Mage::registry('magehit_sociallogin_twitter_userdetails');



        if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {

            $redirect = Mage::helper('core/url')->getCurrentUrl();      

        }



        // Redirect uri

        Mage::getSingleton('core/session')->setTwitterRedirect($redirect);



        $this->setTemplate('magehit/sociallogin/twitter/button.phtml');

    }



    protected function _getButtonUrl()

    {

        if(empty($this->userInfo)) {

            return $this->client->createAuthUrl();

        } else {

            return $this->getUrl('mhsociallogin/twitter/disconnect');

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


                $text = "mh_twitter_connect";


        } else {

                $text = "mh_twitter_disconnect";

        }

        

        return $text;

    }


}

