<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 11/11/2014
 * Time: 10:04 πμ
 * Copyright: 2014 Panagiotis Vagenas
 */

namespace acsws\classes\soap;

if (!defined('_PS_VERSION_'))
  exit;

class ACSSoapAreaService extends ACSSoap{
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
		try{
			$result = $this->__soapCall(__FUNCTION__, array_merge($this->clientOptions, $this->params));
		}catch (\Exception $e){
			$result = false;
		}
		return $result;
	}

	public function getByZipCode(){
		if(!$this->isReadyForCall()){
			return false;
		}
		try{
			$result = $this->__soapCall(__FUNCTION__, array_merge($this->clientOptions, $this->params));
		}catch (\Exception $e){
			$result = false;
		}
		return $result;
	}
}