<?php

class Magecheckout_SecuredCheckout_Model_Adminhtml_Observer
{
    /**
     * @return $this
     */
    public function adminhtmlSystemConfigSave()
    {
        $section = Mage::app()->getRequest()->getParam('section');
        if ($section == 'securedcheckout') {
            $websiteCode   = Mage::app()->getRequest()->getParam('website');
            $storeCode     = Mage::app()->getRequest()->getParam('store');
            $css_generator = Mage::getSingleton('securedcheckout/generator_css');
            $css_generator->generateCss($websiteCode, $storeCode, 'design');
            Mage::helper('securedcheckout')->saveUrlRewrite($storeCode);
        }

        return $this;
    }

    /**
     * @param $observer
     */

    public function loadLayoutBefore($observer)
    {
        $event = $observer->getEvent();
        if (!$event)
            return $this;
        $fullActionName = $event->getAction()->getFullActionName();
        $section        = $event->getAction()->getRequest()->getParam('section', false);
        $layout         = $event->getLayout();
        if ($fullActionName === 'adminhtml_system_config_edit' &&
            $section === 'securedcheckout'
        ) {
            $layout->getUpdate()->addHandle('editor');
        }
    }


}