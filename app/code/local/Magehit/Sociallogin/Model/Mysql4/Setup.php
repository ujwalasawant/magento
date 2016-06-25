<?php

class Magehit_Sociallogin_Model_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
    protected $_customerAttributes = array();

    public function setSocialCustomerAttributes($customerAttributes)
    {
        $this->_customerAttributes = $customerAttributes;

        return $this;
    }
    
   /**
     * Add our custom customer attributes
     *
     * @return Mage_Eav_Model_Entity_Setup
     */
    public function installSocialCustomerAttributes()
    {        
        foreach ($this->_customerAttributes as $code => $attr) {
            $this->addAttribute('customer', $code, $attr);
        }

        return $this;
    }

    /**
     * Remove custom customer attributes
     *
     * @return Mage_Eav_Model_Entity_Setup
     */
    public function removeSocialCustomerAttributes()
    {
        foreach ($this->_customerAttributes as $code => $attr) {
            $this->removeAttribute('customer', $code);
        }

        return $this;
    }  
}
