<?php

class Ship200_Onebyone_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View{

	

	public function __construct()

    {

        parent::__construct();

		

		$this->_addButton('pdf_label', array(

            'label'     => Mage::helper('sales')->__('Make Return PDF Label'),

            'onclick'   => 'open_ship200_return();;',

			'class'     => 'btn grey return',

        ));

		

		$this->_addButton('shipping_label', array(

            'label'     => Mage::helper('sales')->__('Create Shipping Label'),

            'onclick'   => 'open_ship200();',

			'class'     => 'btn grey ship',

        ));

		

	}

	

}

?>