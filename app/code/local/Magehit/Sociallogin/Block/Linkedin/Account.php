<?php

class Magehit_Sociallogin_Block_Linkedin_Account extends Mage_Core_Block_Template

{

    protected $client = null;

    protected $userDetails = null;



    protected function _construct() {

        parent::_construct();



        $this->client = Mage::getSingleton('sociallogin/linkedin_client');

        if(!($this->client->isEnabled())) {

            return;

        }


        $this->userInfo = Mage::registry('magehit_sociallogin_linkedin_userdetails');

        $this->setTemplate('magehit/sociallogin/linkedin/account.phtml');

    }



    protected function _hasUserInfo()

    {

        return (bool) $this->userInfo;

    }



    protected function _getLinkedinId()

    {

        return $this->userInfo->id;

    }



    protected function _getStatus()

    {

        if(!empty($this->userInfo->link)) {

            $link = '<a style="background-image:none !important" href="'.$this->userInfo->link.'" target="_blank">'.

                    $this->htmlEscape($this->userInfo->headline).'</a>';

        } else {

            $link = $this->userInfo->headline;

        }



        return $link;

    }



    protected function _getEmail()

    {

        return $this->userInfo->emailAddress;

    }



    protected function _getPicture()

    {

        if(!empty($this->userInfo->pictureUrl)) {

            return Mage::helper('sociallogin/linkedin')

                    ->getProperDimensionsPictureUrl($this->userInfo->id,

                            $this->userInfo->pictureUrl);

        }



        return null;

    }



    protected function _getName()

    {

        return $this->userInfo->headline;

    }



    protected function _getGender()

    {

        if(!empty($this->userInfo->gender)) {

            return ucfirst($this->userInfo->gender);

        }



        return null;

    }



    protected function _getBirthday()

    {

        if(!empty($this->userInfo->birthday)) {

            $birthday = date('F j, Y', strtotime($this->userInfo->birthday));

            return $birthday;

        }



        return null;

    }



}