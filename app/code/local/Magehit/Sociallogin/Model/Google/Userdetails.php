<?php

class Magehit_Sociallogin_Model_Google_Userdetails

{

    protected $client = null;

    protected $userInfo = null;



    public function __construct() {

        if(!Mage::getSingleton('customer/session')->isLoggedIn())

            return;



        $this->client = Mage::getSingleton('sociallogin/google_client');

        if(!($this->client->isEnabled())) {

            return;

        }



        $customer = Mage::getSingleton('customer/session')->getCustomer();
        
        if(($sociallogintGid = $customer->getMagehitSocialloginGid()) &&

                ($socialloginGtoken = $customer->getMagehitSocialloginGtoken())) {

            $helper = Mage::helper('sociallogin/google');



            try{

                $this->client->setAccessToken($socialloginGtoken);



                $this->userInfo = $this->client->api('/userinfo');



                /* The access token may have been updated automatically due to

                 * access type 'offline' */

                $customer->setMagehitSocialloginGtoken($this->client->getAccessToken());

                $customer->save();



            } catch(Magehit_Sociallogin_GoogleOAuthException $e) {

                $helper->disconnect($customer);

                Mage::getSingleton('core/session')->addNotice($e->getMessage());

            } catch(Exception $e) {

                $helper->disconnect($customer);

                Mage::getSingleton('core/session')->addError($e->getMessage());

            }



        }

    }



    public function getUserDetails()

    {

        return $this->userInfo;

    }

}