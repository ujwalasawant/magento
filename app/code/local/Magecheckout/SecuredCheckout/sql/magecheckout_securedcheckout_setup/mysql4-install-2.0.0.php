<?php

/**
 * @var Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();
try {
    /*Add giftwrap to sales_order table*/
    $installer->getConnection()->addColumn($this->getTable('sales/order'), 'mc_giftwrap_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/order'), 'mc_giftwrap_base_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'mc_giftwrap_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'mc_giftwrap_base_amount', 'decimal(12,4) NOT NULL default 0');
    /*Add giftwrap to sales_order_invoice tabel*/
    $installer->getConnection()->addColumn($this->getTable('sales/invoice'), 'mc_giftwrap_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/invoice'), 'mc_giftwrap_base_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/invoice_item'), 'mc_giftwrap_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/invoice_item'), 'mc_giftwrap_base_amount', 'decimal(12,4) NOT NULL default 0');
    /*Add giftwrap to sales_order_creditmemo tabel*/
    $installer->getConnection()->addColumn($this->getTable('sales/creditmemo'), 'mc_giftwrap_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/creditmemo'), 'mc_giftwrap_base_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/creditmemo_item'), 'mc_giftwrap_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/creditmemo_item'), 'mc_giftwrap_base_amount', 'decimal(12,4) NOT NULL default 0');
} catch (Exception $e) {
}
/*Insert Magecheckout OSC Static Blocks*/
$this->importCmsStaticBlocks();
$this->importCmsPages();
//$this->saveUrlRewrite(); /** Brian disabled */
$installer->endSetup();
