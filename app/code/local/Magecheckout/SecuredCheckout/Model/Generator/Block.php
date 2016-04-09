<?php

/**
 * Magecheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magecheckout.com license that is
 * available through the world-wide-web at this URL:
 * http://wiki.magecheckout.com/general/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 * @copyright   Copyright (c) 2015 Magecheckout (http://www.magecheckout.com/)
 * @license     http://wiki.magecheckout.com/general/license.html
 */
class Magecheckout_SecuredCheckout_Model_Generator_Block extends Mage_Core_Model_Abstract
{
    /**
     * Path to directory with import files
     *
     * @var string
     */
    protected $_importPath;

    /**
     * Create path
     */
    public function __construct()
    {
        parent::__construct();
        $this->_importPath = Mage::getBaseDir() . '/app/code/local/Magecheckout/SecuredCheckout/etc/generator/';
    }

    /**
     * Import CMS items
     *
     * @param string model string
     * @param string name of the main XML node (and name of the XML file)
     * @param bool overwrite existing items
     */
    public function importBlocks($modelString, $itemContainerNodeString, $overwrite = false)
    {
        try {
            $xmlPath = $this->_importPath . $itemContainerNodeString . '.xml';
            if (!is_readable($xmlPath)) {
                throw new Exception(
                    Mage::helper('securedcheckout')->__("Can't read data file: %s", $xmlPath)
                );
            }
            $xmlObj              = new Varien_Simplexml_Config($xmlPath);
            $conflictingOldItems = array();
            $i                   = 0;
            foreach ($xmlObj->getNode($itemContainerNodeString)->children() as $b) {
                //Check if block already exists
                $oldBlocks = Mage::getModel($modelString)->getCollection()
                    ->addFieldToFilter('identifier', $b->identifier)
                    ->load();

                //If items can be overwritten
                if ($overwrite) {
                    if (count($oldBlocks) > 0) {
                        $conflictingOldItems[] = $b->identifier;
                        foreach ($oldBlocks as $old)
                            $old->delete();
                    }
                } else {
                    if (count($oldBlocks) > 0) {
                        $conflictingOldItems[] = $b->identifier;
                        continue;
                    }
                }

                Mage::getModel($modelString)
                    ->setTitle($b->title)
                    ->setContent($b->content)
                    ->setIdentifier($b->identifier)
                    ->setIsActive($b->is_active)
                    ->setStores(array(0))
                    ->save();
                $i++;
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::logException($e);
        }
    }

}
