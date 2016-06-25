<?php
class Magehit_Sociallogin_Helper_Instagram extends Mage_Core_Helper_Abstract
{
    public function disconnect(Mage_Customer_Model_Customer $customer)
    {
        $client = Mage::getSingleton('sociallogin/instagram_client');
    }
    public function connectByInstagramId(Mage_Customer_Model_Customer $customer, $instagramId, $token)
    {
        $customer->setMagehitSocialloginIgid($instagramId)->setMagehitSocialloginIgtoken($token)->save();
        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }
    public function connectByCreatingAccount($email, $firstName, $lastName, $instagramId, $token)
    {
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())->setEmail($email)->setFirstname($firstName)->setLastname($lastName)->setMagehitSocialloginIgid($instagramId)->setMagehitSocialloginIgtoken($token)->setPassword($customer->generatePassword(10))->save();
        $customer->setConfirmation(null);
        $customer->save();
        $customer->sendNewAccountEmail('confirmed', '', Mage::app()->getStore()->getId());
        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }
    public function loginByCustomer(Mage_Customer_Model_Customer $customer)
    {
        if ($customer->getConfirmation()) {
            $customer->setConfirmation(null);
            $customer->save();
        }
        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }
    public function getCustomersByInstagramId($instagramId)
    {
        $customer   = Mage::getModel('customer/customer');
        $collection = $customer->getCollection()->addAttributeToFilter('magehit_sociallogin_igid', $instagramId)->setPageSize(1);
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId());
        }
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $collection->addFieldToFilter('entity_id', array(
                'neq' => Mage::getSingleton('customer/session')->getCustomerId()
            ));
        }
        return $collection;
    }
    public function getCustomersByEmail($email)
    {
        $customer   = Mage::getModel('customer/customer');
        $collection = $customer->getCollection()->addFieldToFilter('email', $email)->setPageSize(1);
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId());
        }
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $collection->addFieldToFilter('entity_id', array(
                'neq' => Mage::getSingleton('customer/session')->getCustomerId()
            ));
        }
        return $collection;
    }
    public function getProperDimensionsPictureUrl($instagramId, $pictureUrl)
    {
        $url       = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'magehit' . '/' . 'sociallogin' . '/' . 'instagram' . '/' . $instagramId;
        $filename  = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'magehit' . DS . 'sociallogin' . DS . 'instagram' . DS . $instagramId;
        $directory = dirname($filename);
        if (!file_exists($directory) || !is_dir($directory)) {
            if (!@mkdir($directory, 0777, true))
                return null;
        }
        $config = array(
            'adapter'      => 'Zend_Http_Client_Adapter_Socket',
            'ssltransport' => 'tls'
        );
        if (!file_exists($filename) || (file_exists($filename) && (time() - filemtime($filename) >= 3600))) {
            $client = new Zend_Http_Client($pictureUrl,$config);
            $client->setStream();
            $response = $client->request('GET');
            stream_copy_to_stream($response->getStream(), fopen($filename, 'w'));
            $imageObj = new Varien_Image($filename);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize(150, 150);
            $imageObj->save($filename);
        }
        return $url;
    }
}
