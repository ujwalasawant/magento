<?php
class Magehit_Sociallogin_Model_Linkedin_Userdetails

{

    protected $client = null;

    protected $userDetails = null;

    protected $userInfoApi = array(
                        'id',
                        'first-name',
                        'last-name',
                        'headline',
                        'picture-url',
                        'email-address',
                        'phone-numbers',
                        'location'
                    );



    public function __construct() {
	

        if(!Mage::getSingleton('customer/session')->isLoggedIn())

            return;



        $this->client = Mage::getSingleton('sociallogin/linkedin_client');

        if(!($this->client->isEnabled())) {
            return;

        }



        $customer = Mage::getSingleton('customer/session')->getCustomer();
        
		
		
        if(($sociallogintLid = $customer->getMagehitSocialloginLid()) &&

                ($socialloginLtoken = $customer->getMagehitSocialloginLtoken())) {

            $helper = Mage::helper('sociallogin/linkedin');



            try{

                $this->client->setAccessToken($socialloginLtoken);


                //$this->userDetails = $this->client->api($this->userInfoApi);
				
				
				$userInfoApi = array(
                        'id',
                        'first-name',
                        'last-name',
                        'headline',
                        'picture-url',
                        'email-address',
                        'phone-numbers',
                        'location'
                    );
					
				$this->userDetails = $this->client->api('/people/~:('.implode(',', $userInfoApi).')?format=json');
				
				/****/



                /* The access token may have been updated automatically due to

                 * access type 'offline' */

                $customer->setMagehitSocialloginGtoken($this->client->getAccessToken());

                $customer->save();



            } catch(Magehit_Sociallogin_LinkedinOAuthException $e) {

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