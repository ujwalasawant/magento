<?php

class Magecheckout_SecuredCheckout_Model_Attribute extends Mage_Core_Model_Abstract
{

    /**
     * get all customer attribute used for onetepcheckout by postion
     *
     * @param null $store
     * @return Magecheckout_SecuredCheckout_Model_Mysql4_Attribute_Collection
     */
    public function getSortedFields()
    {
        $attributeArray = array(
            array(
                'attribute_code' => 'firstname',
                'field_option'   => 'req',
                'sort_order'     => 1,
                'colspan'        => 1,
                'entity_type_id' => 2
            ),
            array(
                'attribute_code' => 'lastname',
                'field_option'   => 'req',
                'sort_order'     => 2,
                'colspan'        => 1,
                'entity_type_id' => 2
            ),
            array(
                'attribute_code' => 'email',
                'field_option'   => 'req',
                'sort_order'     => 3,
                'colspan'        => 2,
                'entity_type_id' => 1
            ),
            array(
                'attribute_code' => 'street',
                'field_option'   => 'req',
                'sort_order'     => 4,
                'colspan'        => 2,
                'entity_type_id' => 2
            ),
            array(
                'attribute_code' => 'country_id',
                'field_option'   => 'req',
                'sort_order'     => 5,
                'colspan'        => 1,
                'entity_type_id' => 2
            ),
            array(
                'attribute_code' => 'city',
                'field_option'   => 'opt',
                'sort_order'     => 6,
                'colspan'        => 1,
                'entity_type_id' => 2
            ),
            array(
                'attribute_code' => 'postcode',
                'field_option'   => 'req',
                'sort_order'     => 7,
                'colspan'        => 1,
                'entity_type_id' => 2
            ),
            array(
                'attribute_code' => 'region',
                'field_option'   => 'opt',
                'sort_order'     => 8,
                'colspan'        => 1,
                'entity_type_id' => 2
            ),
            array(
                'attribute_code' => 'telephone',
                'field_option'   => 'req',
                'sort_order'     => 9,
                'colspan'        => 1,
                'entity_type_id' => 2
            ),
            array(
                'attribute_code' => 'company',
                'field_option'   => 'opt',
                'sort_order'     => 10,
                'colspan'        => 1,
                'entity_type_id' => 2
            )
        );
        $container      = new Varien_Object(
            array(
                'attribute_array' => $attributeArray
            )
        );
        Mage::dispatchEvent('secured_checkout_prepare_attribute_array_before', array(
            'container' => $container
        ));
        $sortedAttributes = $this->sortAttributes($container->getData('attribute_array'));
        $attributes       = new Varien_Data_Collection();
        foreach ($sortedAttributes as $attribute) {
            $item = new Varien_Object($attribute);
            $attributes->addItem($item);

        }

        return $attributes;
    }

    /**
     * Sort Attribute By Position
     *
     * @param $attributes
     * @return mixed
     */
    public function sortAttributes($attributes)
    {
        $sortArray = array();

        foreach ($attributes as $attribute) {
            foreach ($attribute as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }
        array_multisort($sortArray['sort_order'], SORT_ASC, $attributes);

        return $attributes;
    }


}