<?php
/**
 * Created by PhpStorm.
 * User: ujwala
 * Date: 23/1/16
 * Time: 4:36 PM
 */
class Mymodule_Customerpage_Block_Review extends Mage_Review_Block_Customer_View
{
    function getProduct( Mage_Review_Model_Review $review ) {
        if( !isset($this->_loadedProducts[ $review->getEntityPkValue() ]) ) {
            $this->_loadedProducts[$review->getEntityPkValue()] = Mage::getModel('catalog/product')->load( $review->getEntityPkValue() );
        }

        return $this->_loadedProducts[ $review->getEntityPkValue() ];
    }
    function getAverageRating( Mage_Review_Model_Review $review ) {
        $avg = 0;
        if( count($review->getRatingVotes()) ) {
            $ratings = array();
            foreach( $review->getRatingVotes() as $rating ) {
                $ratings[] = $rating->getPercent();
            }
            $avg = array_sum($ratings)/count($ratings);
        }

        return $avg;
    }

}