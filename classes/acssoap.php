<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 10/11/2014
 * Time: 2:21 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */
if (!defined('_PS_VERSION_'))
  exit;
  
abstract class ACSSoap extends SoapClient{
	protected $wsdl;
	protected $requiredParams = array();
	protected $params = array();

	public $clientOptions = array(
		'companyId' => '997942446',
		'companyPass' => '8730',
		'username' => 'info',
		'password' => '1061',
		'customerId' => '2ΒΣ60129',
	);

	public function __construct($wsdl, Array $clientOptions){
		$this->clientOptions = $clientOptions;
		parent::__construct($wsdl, $clientOptions);
	}

	public function setParams(Array $params){
		foreach ( $this->requiredParams as $k => $v ) {
			if(!isset($params[$v])){
				return null;
			}
			$this->params[$v] = $params[$v];
		}
		return $this;
	}

	protected function isReadyForCall(){
		return !(empty($this->wsdl) || empty($this->clientOptions) || empty($this->params));
	}
}

class ACSSoapPriceCalcultation extends ACSSoap{
	protected $wsdl = 'https://services.acscourier.net/ACSPriceCalculation-portlet/axis/Plugin_ACSPriceCalculation_ACSPriceService?wsdl';
	protected $requiredParams = array(
		'st_from',
		'st_to',
		'varos',
		'itemType',
		'width',
		'height',
		'length',
		'date_par',
		'products',
		'xrewsh',
		'zone',
		'asf_poso',
		'invoiceCountry',
		'lang'
	);

	public function __construct(Array $clientOptions){
		parent::__construct($this->wsdl, $clientOptions);
	}

	public function getPrice(){
		if(!$this->isReadyForCall()){
			return false;
		}
		return $this->__soapCall(__FUNCTION__, array_merge($this->clientOptions, $this->params));
	}

	public function getPriceByVolume(){
		if(!$this->isReadyForCall()){
			return false;
		}
		return $this->__soapCall(__FUNCTION__, array_merge($this->clientOptions, $this->params));
	}

	public function getPriceNew(){
		if(!$this->isReadyForCall()){
			return false;
		}
		return $this->__soapCall(__FUNCTION__, array_merge(array_values($this->clientOptions), array_values($this->params)));
	}
}

class ACSSoapArreaService extends ACSSoap{
	protected $wsdl = 'https://services.acscourier.net/ACS-AddressValidationNew-portlet/axis/Plugin_ACSAddressValidation_ACSAreaService?wsdl';
	protected $requiredParams = array(
		'zip_code',
		'only_dp'
	);

	public function __construct(Array $clientOptions){
		parent::__construct($this->wsdl, $clientOptions);
	}

	public function findByZipCode(){
		if(!$this->isReadyForCall()){
			return false;
		}
		return $this->__soapCall(__FUNCTION__, array_merge(array_values($this->clientOptions), array_values($this->params)));
	}

	public function getByZipCode(){
		if(!$this->isReadyForCall()){
			return false;
		}
		return $this->__soapCall(__FUNCTION__, array_merge(array_values($this->clientOptions), array_values($this->params)));
	}
}