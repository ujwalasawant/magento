<?php

abstract class Mage_Usa_Model_Shipping_Carrier_Fedex extends Mage_Core_Model_Abstract implements Mage_Shipping_Model_Carrier_Interface
 {

     protected $_code = 'fedex';

     protected $_request = null;

     protected $_result = null;

     protected $_gatewayUrl = 'https://wsbeta.fedex.com:443/web-services/';

     public function collectRates(Mage_Shipping_Model_Rate_Request $request)
     {
              if (!$this->getConfigFlag('active')) {
                        return false;
        }

        $this->setRequest($request);

        $this->_result = $this->_getQuotes();

        $this->_updateFreeMethodQuote($request);

        return $this->getResult();
    }

    public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {
         $this->_request = $request;

        $r = new Varien_Object();

        if ($request->getLimitMethod()) {
               $r->setService($request->getLimitMethod());
        }
        if ($request->getFedexAccount()) {
             $account = $request->getFedexAccount();
        } else {
                $account = $this->getConfigData('account');
        }
        $r->setAccount($account);
        if ($request->getFedexDropoff()) {
                 $dropoff = $request->getFedexDropoff();
        } else {
               $dropoff = $this->getConfigData('dropoff');
        }
        $r->setDropoffType($dropoff);
        if ($request->getFedexPackaging()) {
             $packaging = $request->getFedexPackaging();
        } else {
                $packaging = $this->getConfigData('packaging');
        }
        $r->setPackaging($packaging);
        if ($request->getOrigCountry()) {
               $origCountry = $request->getOrigCountry();
        } else {
                $origCountry = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
        }
        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());
        if ($request->getOrigPostcode()) {
           $r->setOrigPostal($request->getOrigPostcode());
        } else {
               $r->setOrigPostal(Mage::getStoreConfig('shipping/origin/postcode', $this->getStore()));
        }
        if ($request->getDestCountryId()) {
              $destCountry = $request->getDestCountryId();
        } else {
                $destCountry = self::USA_COUNTRY_ID;
        }
        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());
        if ($request->getDestPostcode()) {
               $r->setDestPostal($request->getDestPostcode());
        } else {

        }
        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight()!= $request->getPackageWeight()) {
               $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }
        $r->setValue($request->getPackageValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());
        $this->_rawRequest = $r;
        return $this;
    }
    public function getResult()
    {
     return $this->_result;
    }
    protected function _getQuotes()
    {
       return $this->_getXmlQuotes();
    }
    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;
        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $r->setWeight($weight);
        $r->setService($freeMethod);
    }
    protected function _getXmlQuotes()
    {
        $r = $this->_rawRequest;
        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><FDXRateAvailableServicesRequest/>');
        $xml->addAttribute('xmlns:api', 'http://www.fedex.com/fsmapi');
        $xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->addAttribute('xsi:noNamespaceSchemaLocation', 'FDXRateAvailableServicesRequest.xsd');
        $requestHeader = $xml->addChild('RequestHeader');
//          $requestHeader->addChild('CustomerTransactionIdentifier', 'CTIString');
            $requestHeader->addChild('AccountNumber', $r->getAccount());
//          $requestHeader->addChild('MeterNumber', '2436351');  -- my own meter number
            $requestHeader->addChild('MeterNumber', '118722378');
          $requestHeader->addChild('CarrierCode', 'FDXE');
//          $requestHeader->addChild('CarrierCode', 'FDXG');
            /**
  00168              *  FDXE � FedEx Express
  00169              *  FDXG � FedEx Ground
  00170              */
        $xml->addChild('ShipDate', date('Y-m-d'));
//      $xml->addChild('ReturnShipmentIndicator', 'NONRETURN');
        /**
  00175          *  � NONRETURN
  00176          *  � PRINTRETURNLABEL
  00177          *  � EMAILLABEL
  00178          */
        $xml->addChild('DropoffType', $r->getDropoffType());
        /**
  00181          *  � REGULARPICKUP
  00182          *  � REQUESTCOURIER
  00183          *  � DROPBOX
  00184          *  � BUSINESSSERVICECENTER
  00185          *  � STATION
  00186          *  Only REGULARPICKUP, REQUESTCOURIER, and STATION are
  00187          *  allowed with international freight shipping.
  00188          */
        if ($r->hasService()) {
           $xml->addChild('Service', $r->getService());
        }
        /**
  00193          *  One of the following FedEx Services is optional:
  00194          *  � PRIORITYOVERNIGHT
  00195          *  � STANDARDOVERNIGHT
  00196          *  � FIRSTOVERNIGHT
  00197          *  � FEDEX2DAY
  00198          *  � FEDEXEXPRESSSAVER
  00199          *  � INTERNATIONALPRIORITY
  00200          *  � INTERNATIONALECONOMY
  00201          *  � INTERNATIONALFIRST
  00202          *  � FEDEX1DAYFREIGHT
  00203          *  � FEDEX2DAYFREIGHT
  00204          *  � FEDEX3DAYFREIGHT
  00205          *  � FEDEXGROUND
  00206          *  � GROUNDHOMEDELIVERY
  00207          *  � INTERNATIONALPRIORITY FREIGHT
  00208          *  � INTERNATIONALECONOMY FREIGHT
  00209          *  � EUROPEFIRSTINTERNATIONALPRIORITY
  00210          *  If provided, only that service�s estimated charges will be returned.
  00211          */
        $xml->addChild('Packaging', $r->getPackaging());
        /**
  00214          *  One of the following package types is required:
  00215          *  � FEDEXENVELOPE
  00216          *  � FEDEXPAK
  00217          *  � FEDEXBOX
  00218          *  � FEDEXTUBE
  00219          *  � FEDEX10KGBOX
  00220          *  � FEDEX25KGBOX
  00221          *  � YOURPACKAGING
  00222          *  If value entered is FEDEXENVELOPE, FEDEX10KGBOX, or
  00223          *  FEDEX25KGBOX, an MPS rate quote is not allowed.
  00224          */
        $xml->addChild('WeightUnits', 'KGS');
        /**
  00227          *  � LBS
  00228          *  � KGS
  00229          *  LBS is required for a U.S. FedEx Express rate quote.
  00230          */
        $xml->addChild('Weight', $r->getWeight());
//      $xml->addChild('ListRate', 'true');
        /**
  00234          *  Optional.
  00235          *  If = true or 1, list-rate courtesy quotes should be returned in addition to
  00236          *  the discounted quote.
  00237          */
        $originAddress = $xml->addChild('OriginAddress');
//          $originAddress->addChild('StateOrProvinceCode', 'GA');   -- ???
            $originAddress->addChild('PostalCode', $r->getOrigPostal());
            $originAddress->addChild('CountryCode', $r->getOrigCountry());
        $destinationAddress = $xml->addChild('DestinationAddress');
//          $destinationAddress->addChild('StateOrProvinceCode', 'GA');   -- ???
            $destinationAddress->addChild('PostalCode', $r->getDestPostal());
            $destinationAddress->addChild('CountryCode', $r->getDestCountry());
        $payment = $xml->addChild('Payment');
            $payment->addChild('PayorType', 'SENDER');
            /**
  00252              *  Optional.
  00253              *  Defaults to SENDER.
  00254              *  If value other than SENDER is used, no rates will still be returned.
  00255              */
        /**
  00258          *  DIMENSIONS
        00259          *
        00260          *  Dimensions / Length
        00261          *  Optional.
        00262          *  Only applicable if the package type is YOURPACKAGING.
        00263          *  The length of a package.
        00264          *  Format: Numeric, whole number
        00265          *
        00266          *  Dimensions / Width
        00267          *  Optional.
        00268          *  Only applicable if the package type is YOURPACKAGING.
        00269          *  The width of a package.
        00270          *  Format: Numeric, whole number
        00271          *
        00272          *  Dimensions / Height
        00273          *  Optional.
        00274          *  Only applicable if the package type is YOURPACKAGING.
        00275          *  The height of a package.
        00276          *  Format: Numeric, whole number
        00277          *
        00278          *  Dimensions / Units
        00279          *  Required if dimensions are entered.
        00280          *  Only applicable if the package type is YOURPACKAGING.
        00281          *  The valid unit of measure codes for the package dimensions are:
        00282          *  IN � Inches
        00283          *  CM � Centimeters
        00284          *  U.S. FedEx Express must be in inches.
        00285          */
        $declaredValue = $xml->addChild('DeclaredValue');
            $declaredValue->addChild('Value', $r->getValue());
//            $declaredValue->addChild('CurrencyCode', 'USD');
            $declaredValue->addChild('CurrencyCode', $this->getCurrencyCode());
        if ($this->getConfigData('residence_delivery')) {
                $specialServices = $xml->addChild('SpecialServices');
                 $specialServices->addChild('ResidentialDelivery', 'true');
        }
//      $specialServices = $xml->addChild('SpecialServices');
//          $specialServices->addChild('Alcohol', 'true');
//          $specialServices->addChild('DangerousGoods', 'true')->addChild('Accessibility', 'ACCESSIBLE');
        /**
  00301          *  Valid values:
  00302          *  ACCESSIBLE � accessible DG
  00303          *  INACCESSIBLE � inaccessible DG
  00304          */
//          $specialServices->addChild('DryIce', 'true');
//          $specialServices->addChild('ResidentialDelivery', 'true');
        /**
  00308          *  If = true or 1, the shipment is Residential Delivery. If Recipient Address
  00309          *  is in a rural area (defined by table lookup), additional charge will be
  00310          *  applied. This element is not applicable to the FedEx Home Delivery
  00311          *  service.
  00312          */
//          $specialServices->addChild('InsidePickup', 'true');
//          $specialServices->addChild('InsideDelivery', 'true');
//          $specialServices->addChild('SaturdayPickup', 'true');
//          $specialServices->addChild('SaturdayDelivery', 'true');
//          $specialServices->addChild('NonstandardContainer', 'true');
//          $specialServices->addChild('SignatureOption', 'true');
        /**
  00320          *  Optional.
  00321          *  Specifies the Delivery Signature Option requested for the shipment.
  00322          *  Valid values:
  00323          *  � DELIVERWITHOUTSIGNATURE
  00324          *  � INDIRECT
  00325          *  � DIRECT
  00326          *  � ADULT
  00327          *  For FedEx Express shipments, the DELIVERWITHOUTSIGNATURE
  00328          *  option will not be allowed when the following special services are
  00329          *  requested:
  00330          *  � Alcohol
  00331          *  � Hold at Location
  00332          *  � Dangerous Goods
  00333          *  � Declared Value greater than $500
  00334          */
        /**
  00337          *  HOMEDELIVERY
  00338          *
  00339          *  HomeDelivery / Type
  00340          *  One of the following values are required for FedEx Home Delivery
  00341          *  shipments:
  00342          *  � DATECERTAIN
  00343          *  � EVENING
  00344          *  � APPOINTMENT
  00345          *
  00346          *  PackageCount
  00347          *  Required for multiple-piece shipments (MPS).
  00348          *  For MPS shipments, 1 piece = 1 box.
  00349          *  For international Freight MPS shipments, this is the total number of
  00350          *  "units." Units are the skids, pallets, or boxes that make up a freight
  00351          *  shipment.
  00352          *  Each unit within a shipment should have its own label.
  00353          *  FDXE only applies to COD, MPS, and international.
  00354          *  Valid values: 1 to 999
  00355          */
        /**
  00358          *  VARIABLEHANDLINGCHARGES
  00359          *
  00360          *  VariableHandlingCharges / Level
  00361          *  Optional.
  00362          *  Only applicable if valid Variable Handling Type is present.
  00363          *  Apply fixed or variable handling charges at package or shipment level.
  00364          *  Valid values:
  00365          *  � PACKAGE
  00366          *  � SHIPMENT
  00367          *  The value "SHIPMENT" is applicable only on last piece of FedEx
  00368          *  Ground or FedEx Express MPS shipment only.
  00369          *  Note: Value "SHIPMENT" = shipment level affects the entire shipment.
  00370          *  Anything else sent in Child will be ignored.
  00371          *
  00372          *  VariableHandlingCharges / Type
  00373          *  Optional.
  00374          *  If valid value is present, a valid Variable Handling Charge is required.
  00375          *  Specifies what type of Variable Handling charges to assess and on
  00376          *  which amount.
  00377          *  Valid values:
  00378          *  � FIXED_AMOUNT
  00379          *  � PERCENTAGE_OF_BASE
  00380          *  � PERCENTAGE_OF_NET
  00381          *  � PERCENTAGE_OF_NET_ EXCL_TAXES
  00382          *
  00383          *  VariableHandlingCharges / AmountOrPercentage
  00384          *  Optional.
  00385          *  Required in conjunction with Variable Handling Type.
  00386          *  Contains the dollar or percentage amount to be added to the Freight
  00387          *  charges. Whether the amount is a dollar or percentage is based on the
  00388          *  Variable Handling Type value that is included in this Request.
  00389          *  Format: Two explicit decimal positions (e.g. 1.00); 10 total length
  00390          *  including decimal place.
  00391          */
        $xml->addChild('PackageCount', '1');
        $request = $xml->asXML();
/*
        $client = new Zend_Http_Client();
        $client->setUri($this->getConfigData('gateway_url'));
        $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
        $client->setParameterPost($request);
        $response = $client->request();
        $responseBody = $response->getBody();
*/
        try {
                $url = $this->getConfigData('gateway_url');
            if (!$url) {
                         $url = $this->_defaultGatewayUrl;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            $responseBody = curl_exec($ch);
            curl_close ($ch);
        } catch (Exception $e) {
                $responseBody = '';
        }
        return $this->_parseXmlResponse($responseBody);
    }
    protected function _parseXmlResponse($response)
    {
        $costArr = array();
        $priceArr = array();
        $errorTitle = 'Unable to retrieve quotes';
        if (strlen(trim($response))>0) {
           if (strpos(trim($response), '<?xml')===0) {
                   $xml = simplexml_load_string($response);
   if (is_object($xml)) {
                 if (is_object($xml->Error) && is_object($xml->Error->Message)) {
                         $errorTitle = (string)$xml->Error->Message;
                    } elseif (is_object($xml->SoftError) && is_object($xml->SoftError->Message)) {
                                         $errorTitle = (string)$xml->SoftError->Message;
                    } else {
                                        $errorTitle = 'Unknown error';
                    }
                    $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
                    foreach ($xml->Entry as $entry) {
                                     if (in_array((string)$entry->Service, $allowedMethods)) {
                                             $costArr[(string)$entry->Service] = (string)$entry->EstimatedCharges->DiscountedCharges->NetCharge;
                            $priceArr[(string)$entry->Service] = $this->getMethodPrice((string)$entry->EstimatedCharges->DiscountedCharges->NetCharge, (string)$entry->Service);
                        }
                    }
                    asort($priceArr);
                }
            } else {
                  $errorTitle = 'Response is in the wrong format';
            }
        }
        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
                $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('fedex');
            $error->setCarrierTitle($this->getConfigData('title'));
            //$error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                    $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('fedex');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($this->getCode('method', $method));
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
        return $result;
    }
/*
    public function isEligibleForFree($method)
    {
        return $method=='FEDEXGROUND';
    }
*/
    public function getCode($type, $code='')
    {
       $codes = array(

   'method'=>array(
                   'PRIORITYOVERNIGHT'                => Mage::helper('usa')->__('Priority Overnight'),
                'STANDARDOVERNIGHT'                => Mage::helper('usa')->__('Standard Overnight'),
                'FIRSTOVERNIGHT'                   => Mage::helper('usa')->__('First Overnight'),
                'FEDEX2DAY'                        => Mage::helper('usa')->__('2Day'),
                'FEDEXEXPRESSSAVER'                => Mage::helper('usa')->__('Express Saver'),
                'INTERNATIONALPRIORITY'            => Mage::helper('usa')->__('International Priority'),
                'INTERNATIONALECONOMY'             => Mage::helper('usa')->__('International Economy'),
                'INTERNATIONALFIRST'               => Mage::helper('usa')->__('International First'),
                'FEDEX1DAYFREIGHT'                 => Mage::helper('usa')->__('1 Day Freight'),
                'FEDEX2DAYFREIGHT'                 => Mage::helper('usa')->__('2 Day Freight'),
                'FEDEX3DAYFREIGHT'                 => Mage::helper('usa')->__('3 Day Freight'),
                'FEDEXGROUND'                      => Mage::helper('usa')->__('Ground'),
                'GROUNDHOMEDELIVERY'               => Mage::helper('usa')->__('Home Delivery'),
                'INTERNATIONALPRIORITY FREIGHT'    => Mage::helper('usa')->__('Intl Priority Freight'),
                'INTERNATIONALECONOMY FREIGHT'     => Mage::helper('usa')->__('Intl Economy Freight'),
                'EUROPEFIRSTINTERNATIONALPRIORITY' => Mage::helper('usa')->__('Europe First Priority'),
            ),
            'dropoff'=>array(
                     'REGULARPICKUP'         => Mage::helper('usa')->__('Regular Pickup'),
                'REQUESTCOURIER'        => Mage::helper('usa')->__('Request Courier'),
                'DROPBOX'               => Mage::helper('usa')->__('Drop Box'),
                'BUSINESSSERVICECENTER' => Mage::helper('usa')->__('Business Service Center'),
                'STATION'               => Mage::helper('usa')->__('Station'),
            ),
            'packaging'=>array(
                     'FEDEXENVELOPE' => Mage::helper('usa')->__('FedEx Envelope'),
                'FEDEXPAK'      => Mage::helper('usa')->__('FedEx Pak'),
                'FEDEXBOX'      => Mage::helper('usa')->__('FedEx Box'),
                'FEDEXTUBE'     => Mage::helper('usa')->__('FedEx Tube'),
                'FEDEX10KGBOX'  => Mage::helper('usa')->__('FedEx 10kg Box'),
                'FEDEX25KGBOX'  => Mage::helper('usa')->__('FedEx 25kg Box'),
                'YOURPACKAGING' => Mage::helper('usa')->__('Your Packaging'),
            ),
        );
        if (!isset($codes[$type])) {
      //            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid FedEx XML code type: %s', $type));
            return false;
        } elseif (''===$code) {
                 return $codes[$type];
        }
        if (!isset($codes[$type][$code])) {
      //            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid FedEx XML code for type %s: %s', $type, $code));
            return false;
        } else {
                return $codes[$type][$code];
        }
    }
    /**
545      *  Return FeDex currency ISO code by Magento Base Currency Code
546      *
547      *  @return   string 3-digit currency code
548      */
    public function getCurrencyCode ()
    {
       $codes = array(
               'DOP' => 'RDD', // Dominican Peso
            'XCD' => 'ECD', // Caribbean Dollars
            'ARS' => 'ARN', // Argentina Peso
            'SGD' => 'SID', // Singapore Dollars
            'KRW' => 'WON', // South Korea Won
            'JMD' => 'JAD', // Jamaican Dollars
            'CHF' => 'SFR', // Swiss Francs
            'JPY' => 'JYE', // Japanese Yen
            'KWD' => 'KUD', // Kuwaiti Dinars
            'GBP' => 'UKL', // British Pounds
            'AED' => 'DHS', // UAE Dirhams
            'MXN' => 'NMP', // Mexican Pesos
            'UYU' => 'UYP', // Uruguay New Pesos
            'CLP' => 'CHP', // Chilean Pesos
            'TWD' => 'NTD', // New Taiwan Dollars
        );
        $currencyCode = Mage::app()->getBaseCurrencyCode();
        return isset($codes[$currencyCode]) ? $codes[$currencyCode] : $currencyCode;
    }
    public function getTracking($trackings)
    {
         $this->setTrackingReqeust();
        if (!is_array($trackings)) {
                 $trackings=array($trackings);
        }
        foreach($trackings as $tracking){
                 $this->_getXMLTracking($tracking);
        }
        return $this->_result;
    }
    protected function setTrackingReqeust()
    {
         $r = new Varien_Object();
        $account = $this->getConfigData('account');
        $r->setAccount($account);
        $this->_rawTrackingRequest = $r;
    }
    protected function _getXMLTracking($tracking)
    {
        $r = $this->_rawTrackingRequest;
        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><FDXTrack2Request/>');
        $xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->addAttribute('xsi:noNamespaceSchemaLocation', 'FDXTrack2Request.xsd');
        $requestHeader = $xml->addChild('RequestHeader');
        $requestHeader->addChild('AccountNumber', $r->getAccount());
        /*
        * for tracking result, actual meter number is not needed
        */
        $requestHeader->addChild('MeterNumber', '118722378');
        $packageIdentifier = $xml->addChild('PackageIdentifier');
        $packageIdentifier->addChild('Value', $tracking);
        /*
        * 0 = summary data, one signle scan structure with the most recent scan
        * 1 = multiple sacn activity for each package
        */
        $xml->addChild('DetailScans', '1');
        $request = $xml->asXML();
        try {
                 $url = $this->getConfigData('gateway_url');
            if (!$url) {
                      $url = $this->_defaultGatewayUrl;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            $responseBody = curl_exec($ch);
            curl_close ($ch);
        } catch (Exception $e) {
                $responseBody = '';
        }
        #echo "<xmp>".$responseBody."</xmp>";
        $this->_parseXmlTrackingResponse($tracking, $responseBody);
    }
    protected function _parseXmlTrackingResponse($trackingvalue,$response)
    {
           $resultArr=array();
         if (strlen(trim($response))>0) {
          if (strpos(trim($response), '<?xml')===0) {
                  $xml = simplexml_load_string($response);
                  if (is_object($xml)) {
                             if (is_object($xml->Error) && is_object($xml->Error->Message)) {
                                     $errorTitle = (string)$xml->Error->Message;
                    } elseif (is_object($xml->SoftError) && is_object($xml->SoftError->Message)) {
                              $errorTitle = (string)$xml->SoftError->Message;
                    }
                  }else{
                                   $errorTitle = 'Error in loading response';
                  }
                  if (!isset($errorTitle)) {
                           $resultArr['status'] = (string)$xml->Package->StatusDescription;
                      $resultArr['service'] = (string)$xml->Package->Service;
                      $resultArr['deliverydate'] = (string)$xml->Package->DeliveredDate;
                      $resultArr['deliverytime'] = (string)$xml->Package->DeliveredTime;
                      $resultArr['deliverylocation'] = (string)$xml->TrackProfile->DeliveredLocationDescription;
                      $resultArr['signedby'] = (string)$xml->Package->SignedForBy;
                      $resultArr['shippeddate'] = (string)$xml->Package->ShipDate;
                      $weight = (string)$xml->Package->Weight;
                      $unit = (string)$xml->Package->WeightUnits;
                      $resultArr['weight'] = "{$weight} {$unit}";
                      $packageProgress = array();
                      if (isset($xml->Package->Event)) {
                                         foreach ($xml->Package->Event as $event) {
                                                 $tempArr=array();
                              $tempArr['activity'] = (string)$event->Description;
                              $tempArr['deliverydate'] = (string)$event->Date;//YYYY-MM-DD
                              $tempArr['deliverytime'] = (string)$event->Time;//HH:MM:ss
                              $addArr=array();
                              if (isset($event->Address->City)) {
                                                         $addArr[] = (string)$event->Address->City;
                              }
                              if (isset($event->Address->StateProvinceCode)) {
                                                        $addArr[] = (string)$event->Address->StateProvinceCode;
                              }
                              if (isset($event->Address->CountryCode)) {
                                                      $addArr[] = (string)$event->Address->CountryCode;
                              }
                              if ($addArr) {
                                                       $tempArr['deliverylocation']=implode(', ',$addArr);
                              }
                              $packageProgress[] = $tempArr;
                          }
                      }
                      $resultArr['progressdetail'] = $packageProgress;
                }
              } else {
                        $errorTitle = 'Response is in the wrong format';
              }
         } else {
              $errorTitle = false;
         }
         if(!$this->_result){
               $this->_result = Mage::getModel('shipping/tracking_result');
         }
         $defaults = $this->getDefaults();
         if($resultArr){
                $tracking = Mage::getModel('shipping/tracking_result_status');
             $tracking->setCarrier('fedex');
             $tracking->setCarrierTitle($this->getConfigData('title'));
             $tracking->setTracking($trackingvalue);
             $tracking->addData($resultArr);
             $this->_result->append($tracking);
         }else{
          $error = Mage::getModel('shipping/tracking_result_error');
            $error->setCarrier('fedex');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setTracking($trackingvalue);
            $error->setErrorMessage($errorTitle ? $errorTitle : Mage::helper('usa')->__('Unable to retrieve tracking'));
            $this->_result->append($error);
         }
    }
    public function getResponse()
    {
          $statuses = '';
        if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result){
              if ($trackings = $this->_result->getAllTrackings()) {
      foreach ($trackings as $tracking){
              if($data = $tracking->getAllData()){
                      if (!empty($data['status'])) {
                              $statuses .= Mage::helper('usa')->__($data['status'])."\n<br/>";
                        } else {
                                           $statuses .= Mage::helper('usa')->__('Empty response')."\n<br/>";
                        }
                    }
                }
            }
        }
        if (empty($statuses)) {
               $statuses = Mage::helper('usa')->__('Empty response');
        }
        return $statuses;
    }
    /**
751      * Get allowed shipping methods
752      *
753      * @return array
754      */
    public function getAllowedMethods()
    {
       $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $arr = array();
        foreach ($allowed as $k) {
                  $arr[$k] = $this->getCode('method', $k);
        }
        return $arr;
    }
}