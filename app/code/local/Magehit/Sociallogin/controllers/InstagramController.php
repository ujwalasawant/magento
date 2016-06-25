<?php
class Magehit_Sociallogin_InstagramController extends Mage_Core_Controller_Front_Action
{
    protected $referer = null;
    /**
    
    * Action connect
    
    */
    public function connectAction()
    {
        try {
            $this->_connectCallback();
        }
        catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        if (Mage::getUrl("customer/account") != "") {
            $this->_redirectUrl(Mage::getUrl("customer/account"));
        } else {
            Mage::helper('sociallogin')->redirect404($this);
        }
    }
    public function disconnectAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        try {
            $this->_disconnectCallback($customer);
        }
        catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        if (!empty($this->referer)) {
            $this->_redirectUrl($this->referer);
        } else {
            Mage::helper('sociallogin')->redirect404($this);
        }
    }
    /**
    
    * disconnect from instagram account
    
    */
    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer)
    {
        $this->referer = Mage::getUrl('mhsociallogin/account/instagram');
        Mage::helper('sociallogin/instagram')->disconnect($customer);
        Mage::getSingleton('core/session')->addSuccess($this->__('You have successfully disconnected your instagram account from our store account.'));
    }
    /**
    
    * connect to instagram account
    
    */
    protected function _connectCallback()
    {
        $client = Mage::getSingleton('sociallogin/instagram_client');
        $code   = $this->getRequest()->getParam('code');
        if ($code) {
            $data                   = $client->getOAuthToken($code);
            $token                  = $data->access_token;
            $name                   = $data->user->username;
            $arrName                = explode(' ', $name, 2);
            $email                  = $name . '@instagram.com';
            Mage::getSingleton('customer/session')->setDataUsername($name);
            Mage::getSingleton('customer/session')->setDataBio($data->user->bio);
            Mage::getSingleton('customer/session')->setDataWebsite($data->user->website);
            Mage::getSingleton('customer/session')->setDataProfilePicture($data->user->profile_picture);
            Mage::getSingleton('customer/session')->setDataFullName($data->user->full_name);
            $customersByInstagramId = Mage::helper('sociallogin/instagram')->getCustomersByInstagramId($data->user->id);
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                // Logged in user
                if ($customersByInstagramId->count()) {
                    // Instagram account already connected to other account - deny
                    Mage::getSingleton('core/session')->addNotice($this->__('Your Instagram account is already connected to one of our store accounts.'));
                    return;
                }
                // Connect from account dashboard - attach
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                Mage::helper('sociallogin/instagram')->connectByInstagramId($customer, $data->user->id, $token);
                Mage::getSingleton('core/session')->addSuccess($this->__('Your Instagram account is now connected to your new user accout at our store. You can login next time by the Instagram SocialLogin button or Store user account. Account confirmation mail has been sent to your email.'));
                return;
            }
            if ($customersByInstagramId->count()) {
                // Existing connected user - login
                $customer = $customersByInstagramId->getFirstItem();
                Mage::helper('sociallogin/instagram')->loginByCustomer($customer);
                Mage::getSingleton('core/session')->addSuccess($this->__('You have successfully logged in using your Instagram account.'));
                return;
            }
            $customersByEmail = Mage::helper('sociallogin/instagram')->getCustomersByEmail($email);
            if ($customersByEmail->count()) {
                // Email account already exists - attach, login
                $customer = $customersByEmail->getFirstItem();
                Mage::helper('sociallogin/instagram')->connectByInstagramId($customer, $data->user->id, $token);
                Mage::getSingleton('core/session')->addSuccess($this->__('We find you already have an account at our store. Your Instagram account is now connected to your store account. Account confirmation mail has been sent to your email.'));
                return;
            }
            // New connection - create, attach, login
            if (empty($data->user->username)) {
                throw new Exception($this->__('Sorry, could not retrieve your Instagram username. Please try again.'));
            }
            Mage::helper('sociallogin/instagram')->connectByCreatingAccount($email, $data->user->username, $data->user->username, $data->user->id, $token);
            Mage::getSingleton('core/session')->addSuccess($this->__('Your Instagram account is now connected to your new user accout at our store. You can login next time by the Instagram SocialLogin button or Store user account. Account confirmation mail has been sent to your email.'));
        }
    }
}