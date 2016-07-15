<?php

class Magehit_AjaxSearch_Model_Mysql4_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    public function getProductCollection($query)
    {
        $attributes = array('name');
        $searchAttributes = Mage::getStoreConfig('ajax_search/general/attributes');
        if (!empty($searchAttributes)) {
            $attributes = explode(',', $searchAttributes);
        }
        $andWhere = array();
        foreach ($attributes as $attribute) {

            $this->addAttributeToSelect($attribute, true);
            foreach (explode(' ', trim($query)) as $word) {
                $andWhere[] = $this->_getAttributeConditionSql(
                    $attribute, array('like' => '%' . $word . '%')
                );
            }
            $this->getSelect()->orWhere(implode(' AND ', $andWhere));
            $andWhere = array();
        }

        return $this;
    }

    public function getCategoryCollection($query, $storeId)
    {
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToSelect('is_active')
            ->setStoreId($storeId);

        $andWhere = array();
        foreach (explode(' ', trim($query)) as $word) {

            $collection->addFieldToFilter(
                'name', array('like'=> '%' . $word .'%')
            );

            if (method_exists($collection, '_getAttributeConditionSql')) {
                $andWhere[] = $collection->_getAttributeConditionSql(
                    'name', array('like' => '%' . $word . '%')
                );
            } else {
                $andWhere[] = $collection->_getConditionSql(
                    'name', array('like' => '%' . $word . '%')
                );
            }
        }

        $collection->getSelect()->orWhere(implode(' AND ', $andWhere));

        return $collection;
    }

    public function getCmsCollection($query, $storeId)
    {
        $collection = Mage::getModel('cms/page')->getCollection()
            ->addStoreFilter($storeId);

        $andWhere = array();
        foreach (explode(' ', trim($query)) as $word) {

            $collection->addFieldToFilter(
                'title', array('like'=> '%' . $word .'%')
            );

            $andWhere[] = $collection->_getConditionSql(
                'title', array('like' => '%' . $word . '%')
            );
        }
        $collection->getSelect()->orWhere(implode(' AND ', $andWhere));

        return $collection;
    }

}