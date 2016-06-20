<?php



class Ship200_Onebyone_Model_Source_Carrier extends Varien_Object

{

	public function toOptionArray()

	{

	    $hlp = Mage::helper('onebyone');

		return array(

			array('value' => "USPS-01", 'label' => $hlp->__('USPS First-Class Mail')),

			array('value' => "USPS-02", 'label' => $hlp->__('USPS Media Mail')),

			array('value' => "USPS-03", 'label' => $hlp->__('USPS Parcel Post')),

			array('value' => "USPS-04", 'label' => $hlp->__('USPS Priority Mail')),

			array('value' => "USPS-05", 'label' => $hlp->__('USPS Express Mail')),

			array('value' => "USPS-06", 'label' => $hlp->__('USPS Express Mail International')),

			array('value' => "USPS-07", 'label' => $hlp->__('USPS Priority Mail International')),

			array('value' => "USPS-08", 'label' => $hlp->__('USPS First Class Mail International')),

			

			array('value' => "UPS-01", 'label' => $hlp->__('UPS Next Day Air')),

			array('value' => "UPS-01-S", 'label' => $hlp->__('UPS Next Day Air Signature Required')),

			array('value' => "UPS-02", 'label' => $hlp->__('UPS Second Day Air')),

			array('value' => "UPS-02-S", 'label' => $hlp->__('UPS Second Day Air Signature Required')),

			array('value' => "UPS-03", 'label' => $hlp->__('UPS Ground')),

			array('value' => "UPS-03-S", 'label' => $hlp->__('UPS Ground Signature Required')),

			array('value' => "UPS-04", 'label' => $hlp->__('UPS Worldwide ExpressSM')),

			array('value' => "UPS-05", 'label' => $hlp->__('UPS Worldwide ExpeditedSM')),

			array('value' => "UPS-06", 'label' => $hlp->__('UPS Standard')),

			array('value' => "UPS-07", 'label' => $hlp->__('UPS Three-Day Select')),

			array('value' => "UPS-07-S", 'label' => $hlp->__('UPS Three-Day Select Signature Required')),

			array('value' => "UPS-08", 'label' => $hlp->__('UPS Next Day Air Saver')),

			array('value' => "UPS-08-S", 'label' => $hlp->__('UPS Next Day Air Saver Signature Required')),

			array('value' => "UPS-09", 'label' => $hlp->__('UPS Next Day Air Early A.M. SM')),

			array('value' => "UPS-09-S", 'label' => $hlp->__('UPS Next Day Air Early A.M. SM Signature Required')),

			array('value' => "UPS-10", 'label' => $hlp->__('UPS Worldwide Express PlusSM')),

			array('value' => "UPS-11", 'label' => $hlp->__('UPS Second Day Air A.M.')),

			array('value' => "UPS-11-S", 'label' => $hlp->__('UPS Second Day Air A.M. Signature Required')),

			array('value' => "UPS-12", 'label' => $hlp->__('UPS Worldwide Saver (Express)')),

			

			array('value' => "Fedex-01", 'label' => $hlp->__('FedEx Ground')),

			array('value' => "Fedex-01-S", 'label' => $hlp->__('FedEx Ground Signature Required')),

			array('value' => "Fedex-02", 'label' => $hlp->__('INTERNATIONAL PRIORITY')),

			array('value' => "Fedex-03", 'label' => $hlp->__('INTERNATIONAL ECONOMY')),

			array('value' => "Fedex-04", 'label' => $hlp->__('FedEx Express Saver')),

			array('value' => "Fedex-04-S", 'label' => $hlp->__('FedEx Express Saver Signature Required')),

			array('value' => "Fedex-05", 'label' => $hlp->__('FedEx 2Day')),

			array('value' => "Fedex-05-S", 'label' => $hlp->__('FedEx 2Day Signature Required')),

			array('value' => "Fedex-06", 'label' => $hlp->__('FedEx 2Day AM')),

			array('value' => "Fedex-06-S", 'label' => $hlp->__('FedEx 2Day AM Signature Required')),

			array('value' => "Fedex-07", 'label' => $hlp->__('FedEx Standard Overnight')),

			array('value' => "Fedex-07-S", 'label' => $hlp->__('FedEx Standard Overnight Signature Required')),

			array('value' => "Fedex-08", 'label' => $hlp->__('FedEx Priority Overnight')),

			array('value' => "Fedex-08-S", 'label' => $hlp->__('FedEx Priority Overnight Signature Required')),

			array('value' => "Fedex-09-S", 'label' => $hlp->__('FedEx First Overnight Signature Required')),

			array('value' => "Fedex-09", 'label' => $hlp->__('FedEx First Overnigh')),

			

			

		);

	}

}