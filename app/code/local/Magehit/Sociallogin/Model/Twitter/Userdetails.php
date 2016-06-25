<?php

class Magehit_Sociallogin_Model_Twitter_Userdetails

{

    protected $client = null;

    protected $userInfo = null;



    public function __construct() {

        if(!Mage::getSingleton('customer/session')->isLoggedIn())

            return;



        $this->client = Mage::getSingleton('sociallogin/twitter_client');

        if(!($this->client->isEnabled())) {

            return;

        }



        $customer = Mage::getSingleton('customer/session')->getCustomer();

        if(($socialloginTid = $customer->getMagehitSocialloginTid()) &&

                ($socialloginTtoken = $customer->getMagehitSocialloginTtoken())) {

            $helper = Mage::helper('sociallogin/twitter');



            try{

                $this->client->setAccessToken($socialloginTtoken);

                

                $this->userInfo = $this->client->api('/account/verify_credentials.json', 'GET', array('skip_status' => true)); 



            }  catch (Magehit_Sociallogin_TwitterOAuthException $e) {

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