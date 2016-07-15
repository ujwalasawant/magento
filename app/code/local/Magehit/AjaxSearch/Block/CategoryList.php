<?php

class Magehit_AjaxSearch_Block_CategoryList extends Mage_Core_Block_Template
{
    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData(array(
            'cache_lifetime' => 86400,
            'cache_tags'     => array(Mage_Catalog_Model_Category::CACHE_TAG)
        ));
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'Magehit_AJAXSEARCH',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            $this->getTemplate(),
            $this->getNameInLayout()
        );
    }

    protected function _beforeToHtml()
    {
        $collection = Mage::getModel('catalog/category')
            ->getCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addNameToResult()
            ->addIsActiveFilter()
            ->addFieldToFilter('level', array('gt' => 1))
            ->addOrder('name', 'ASC');

        if (method_exists($collection, 'addStoreFilter')) {
            $collection->addStoreFilter();
        } else {
            $rootId = Mage::app()->getStore()->getRootCategoryId();
            $root   = Mage::getModel('catalog/category')->load($rootId);
            $collection->addFieldToFilter('path', array('like' => "{$root->getPath()}/%"));
        }

        $this->setCategoryCollection($collection);

        return parent::_beforeToHtml();
    }
}