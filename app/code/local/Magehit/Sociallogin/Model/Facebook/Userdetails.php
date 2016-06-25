<?php

class Magehit_Sociallogin_Model_Facebook_Userdetails

{
    protected $client = null;

    protected $userDetails = null;



    public function __construct() {

        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            return;
        }


        $this->client = Mage::getSingleton('sociallogin/facebook_client');

        if(!($this->client->isEnabled())) {
            return;

        }



        $customer = Mage::getSingleton('customer/session')->getCustomer();
         mage::log($customer->getData());
        if(($socialLoginFid = $customer->getMagehitSocialloginFid()) &&

                ($socialLoginFtoken = $customer->getMagehitSocialloginFtoken())) {

            $helper = Mage::helper('sociallogin/facebook');



            try{

                $this->client->setAccessToken($socialLoginFtoken);

                $this->userDetails = $this->client->api(

                    '/me',

                    'GET',

                    array(

                        'fields' =>

                        'id,name,first_name,last_name,link,birthday,gender,email,picture.type(large)'

                    )

                );

                mage::log($this->userDetails);
            } catch(FacebookOAuthException $e) {
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

        return $this->userDetails;

    }

}