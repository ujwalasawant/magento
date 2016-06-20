<?php
class Ship200_Onebyone_Block_Adminhtml_Ship200 extends Mage_Adminhtml_Block_Widget_Form
{
  	protected $_storeModel;
    protected $_attributes;
    protected $_addMapButtonHtml;
    protected $_removeMapButtonHtml;
    protected $_shortDateFormat;
 
	
  public function __construct()
  {
	parent::__construct();
	$this->setTemplate('onebyone/ship200.phtml');
  }
 	
	public function getAttributes($entityType)
    {
       //$methods = array(array('value'=>'','label'=>Mage::helper('adminhtml')->__('--Choose Magento Shipping Method--')));
		
		$methods[""] = Mage::helper('adminhtml')->__('--Choose Magento Shipping Method--');
		
        $activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach($activeCarriers as $carrierCode => $carrierModel)
        {
           $options = array();
           if( $carrierMethods = $carrierModel->getAllowedMethods() )
           {
               foreach ($carrierMethods as $methodCode => $method)
               {
                    $code= $carrierCode.'_'.$methodCode;
                    //$options[]=array('value'=>$code,'label'=>$method);
					//$options[$code]=$method;

               }
               $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');

           }
           //$methods[]=array('value'=>$code,'label'=>$carrierTitle);
		   $methods[$code]=$carrierTitle;
        }
		
		Zend_Debug::dump($methods);
        return $methods;
    }

    public function getValue($key, $default='', $defaultNew = null)
    {
        if (null !== $defaultNew) {
            if (0 == $this->getProfileId()) {
                $default = $defaultNew;
            }
        }

        $value = $this->getData($key);
        return $this->htmlEscape(strlen($value) > 0 ? $value : $default);
    }
	public function getAddMapButtonHtml()
    {
        if (!$this->_addMapButtonHtml) {
            $this->_addMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('add')->setLabel($this->__('Add Field Mapping'))
                ->setOnClick("addFieldMapping()")->toHtml();
        }
        return $this->_addMapButtonHtml;
    }

    public function getRemoveMapButtonHtml()
    {
        if (!$this->_removeMapButtonHtml) {
            $this->_removeMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('delete')->setLabel($this->__('Remove'))
                ->setOnClick("removeFieldMapping(this)")->toHtml();
        }
        return $this->_removeMapButtonHtml;
    }
	
	public function getFileData(){
	
		$returnArray=array();
		$hlp = Mage::helper('onebyone');
		
		//return array("USPS-01"=>'USPS First-Class Mail');
		
		$returnArray[""]=$hlp->__('Choose Ship200 shipping Method');
		$returnArray["USPS-01"]=$hlp->__('USPS First-Class Mail');
		$returnArray["USPS-02"]=$hlp->__('USPS Media Mail');
		$returnArray["USPS-03"]=$hlp->__('USPS Parcel Post');
		$returnArray["USPS-04"]=$hlp->__('USPS Priority Mail');
		$returnArray["USPS-05"]=$hlp->__('USPS Express Mail');
		$returnArray["USPS-06"]=$hlp->__('USPS Express Mail International');
		$returnArray["USPS-07"]=$hlp->__('USPS Priority Mail International');
		$returnArray["USPS-08"]=$hlp->__('USPS First Class Mail International');
		$returnArray["UPS-01"]=$hlp->__('UPS Next Day Air');
		$returnArray["UPS-01-S"]=$hlp->__('UPS Next Day Air Signature Required');
		$returnArray["UPS-02"]=$hlp->__('UPS Second Day Air');
		$returnArray["UPS-02-S"]=$hlp->__('UPS Second Day Air Signature Required');
		$returnArray["UPS-03"]=$hlp->__('UPS Ground');
		$returnArray["UPS-03-S"]=$hlp->__('UPS Ground Signature Required');
		$returnArray["UPS-04"]=$hlp->__('UPS Worldwide ExpressSM');
		$returnArray["UPS-05"]=$hlp->__('UPS Worldwide ExpeditedSM');
		$returnArray["UPS-06"]=$hlp->__('UPS Standard');
		$returnArray["UPS-07"]=$hlp->__('UPS Three-Day Select');
		$returnArray["UPS-07-S"]=$hlp->__('UPS Three-Day Select Signature Required');
		$returnArray["UPS-08"]=$hlp->__('UPS Next Day Air Saver');
		$returnArray["UPS-08-S"]=$hlp->__('UPS Next Day Air Saver Signature Required');
		$returnArray["UPS-09"]=$hlp->__('UPS Next Day Air Early A.M. SM');
		$returnArray["UPS-09-S"]=$hlp->__('UPS Next Day Air Early A.M. SM Signature Required');
		$returnArray["UPS-10"]=$hlp->__('UPS Worldwide Express PlusSM');
		$returnArray["UPS-11"]=$hlp->__('UPS Second Day Air A.M.');
		$returnArray["UPS-11-S"]=$hlp->__('UPS Second Day Air A.M. Signature Required');
		$returnArray["UPS-12"]=$hlp->__('UPS Worldwide Saver (Express)');
		$returnArray["Fedex-01"]=$hlp->__('FedEx Ground');
		$returnArray["Fedex-01-S"]=$hlp->__('FedEx Ground Signature Required');
		$returnArray["Fedex-02"]=$hlp->__('INTERNATIONAL PRIORITY');
		$returnArray["Fedex-03"]=$hlp->__('INTERNATIONAL ECONOMY');
		$returnArray["Fedex-04"]=$hlp->__('FedEx Express Saver');
		$returnArray["Fedex-04-S"]=$hlp->__('FedEx Express Saver Signature Required');
		$returnArray["Fedex-05"]=$hlp->__('FedEx 2Day');
		$returnArray["Fedex-05-S"]=$hlp->__('FedEx 2Day Signature Required');
		$returnArray["Fedex-06"]=$hlp->__('FedEx 2Day AM');
		$returnArray["Fedex-06-S"]=$hlp->__('FedEx 2Day AM Signature Required');
		$returnArray["Fedex-07"]=$hlp->__('FedEx Standard Overnight');
		$returnArray["Fedex-07-S"]=$hlp->__('FedEx Standard Overnight Signature Required');
		$returnArray["Fedex-08"]=$hlp->__('FedEx Priority Overnight');
		$returnArray["Fedex-08-S"]=$hlp->__('FedEx Priority Overnight Signature Required');
		$returnArray["Fedex-09-S"]=$hlp->__('FedEx First Overnight Signature Required');
		$returnArray["Fedex-09"]=$hlp->__('FedEx First Overnigh');




		return $returnArray;

		
		
		return array(

			""=> $hlp->__('Choose Ship200 shipping Method'),
			
			"USPS-01"=> $hlp->__('USPS First-Class Mail'),

			"USPS-02" => $hlp->__('USPS Media Mail'),

			"USPS-03" => $hlp->__('USPS Parcel Post'),

			"USPS-04" => $hlp->__('USPS Priority Mail'),

			"USPS-05" => $hlp->__('USPS Express Mail'),

			"USPS-06" => $hlp->__('USPS Express Mail International'),

			"USPS-07" => $hlp->__('USPS Priority Mail International'),

			"USPS-08" => $hlp->__('USPS First Class Mail International'),

			

			"UPS-01" => $hlp->__('UPS Next Day Air'),

			"UPS-01-S" => $hlp->__('UPS Next Day Air Signature Required'),

			"UPS-02" => $hlp->__('UPS Second Day Air'),

			"UPS-02-S" => $hlp->__('UPS Second Day Air Signature Required'),

			"UPS-03" => $hlp->__('UPS Ground'),

			"UPS-03-S" => $hlp->__('UPS Ground Signature Required'),

			"UPS-04" => $hlp->__('UPS Worldwide ExpressSM'),

			"UPS-05" => $hlp->__('UPS Worldwide ExpeditedSM'),

			"UPS-06" => $hlp->__('UPS Standard'),

			"UPS-07" => $hlp->__('UPS Three-Day Select'),

			"UPS-07-S" => $hlp->__('UPS Three-Day Select Signature Required'),

			"UPS-08" => $hlp->__('UPS Next Day Air Saver'),

			"UPS-08-S" => $hlp->__('UPS Next Day Air Saver Signature Required'),

			"UPS-09" => $hlp->__('UPS Next Day Air Early A.M. SM'),

			"UPS-09-S" => $hlp->__('UPS Next Day Air Early A.M. SM Signature Required'),

			"UPS-10" => $hlp->__('UPS Worldwide Express PlusSM'),

			"UPS-11" => $hlp->__('UPS Second Day Air A.M.'),

			"UPS-11-S" => $hlp->__('UPS Second Day Air A.M. Signature Required'),

			"UPS-12" => $hlp->__('UPS Worldwide Saver (Express)'),

			

			"Fedex-01" => $hlp->__('FedEx Ground'),

			"Fedex-01-S" => $hlp->__('FedEx Ground Signature Required'),

			"Fedex-02" => $hlp->__('INTERNATIONAL PRIORITY'),

			"Fedex-03" => $hlp->__('INTERNATIONAL ECONOMY'),

			"Fedex-04" => $hlp->__('FedEx Express Saver'),

			"Fedex-04-S" => $hlp->__('FedEx Express Saver Signature Required'),

			"Fedex-05" => $hlp->__('FedEx 2Day'),

			"Fedex-05-S" => $hlp->__('FedEx 2Day Signature Required'),

			"Fedex-06" => $hlp->__('FedEx 2Day AM'),

			"Fedex-06-S" => $hlp->__('FedEx 2Day AM Signature Required'),

			"Fedex-07" => $hlp->__('FedEx Standard Overnight'),

			"Fedex-07-S" => $hlp->__('FedEx Standard Overnight Signature Required'),

			"Fedex-08" => $hlp->__('FedEx Priority Overnight'),

			"Fedex-08-S" => $hlp->__('FedEx Priority Overnight Signature Required'),

			"Fedex-09-S" => $hlp->__('FedEx First Overnight Signature Required'),

			"Fedex-09" => $hlp->__('FedEx First Overnigh'),

			

			

		);
		
					
	}
	
}