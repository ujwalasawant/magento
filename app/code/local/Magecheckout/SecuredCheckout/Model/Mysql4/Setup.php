<?php
/**
 * MageCheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageCheckout.com license that is
 * available through the world-wide-web at this URL:
 * http://magecheckout.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageCheckout
 * @package     Magecheckout_SecuredCheckout
 * @copyright   Copyright (c) 2015 MageCheckout (http://magecheckout.com/)
 * @license     http://magecheckout.com/license-agreement/
 */

/**
 * SecuredCheckout Resource Model
 *
 * @category    MageCheckout
 * @package     Magecheckout_SecuredCheckout
 * @author      MageCheckout Developer
 */
class Magecheckout_SecuredCheckout_Model_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Insert Magecheckout Static block
     */
    public function  importCmsStaticBlocks()
    {
        Mage::getSingleton('securedcheckout/generator_block')->importBlocks('cms/block', 'blocks', true);
    }

    public function  importCmsPages()
    {
        Mage::getSingleton('securedcheckout/generator_block')->importBlocks('cms/page', 'pages', true);
    }

    public function saveUrlRewrite()
    {
        $path    = Magecheckout_SecuredCheckout_Helper_Data::SECURED_CHECKOUT_URL_REWRITE_ID_PATH;
        $storeId = Mage::app()->getStore()->getId();
        $rewrite = Mage::getModel('core/url_rewrite')
            ->loadByIdPath($path);
        if (!$rewrite->getId()) {
            $rewrite->setIdPath($path);
        }
        $rewrite
            ->setStoreId($storeId)
            ->setRequestPath('securecheckout')
            ->setTargetPath('onestepcheckout');
        try {
            $rewrite->save();
        } catch (Exception $e) {
        }

        return $this;
    }

}
