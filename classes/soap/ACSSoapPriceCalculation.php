<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 11/11/2014
 * Time: 10:21 πμ
 * Copyright: 2014 Panagiotis Vagenas
 */

namespace acsws\classes\soap;

if (!defined('_PS_VERSION_'))
  exit;

class ACSSoapPriceCalculation extends ACSSoap{
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
		return $this->checkWeight()->__soapCall(__FUNCTION__, array_merge($this->clientOptions, $this->params));
	}

	public function getPriceByVolume(){
		if(!$this->isReadyForCall()){
			return false;
		}
		return $this->checkWeight()->__soapCall(__FUNCTION__, array_merge($this->clientOptions, $this->params));
	}

	public function getPriceNew(){
		if(!$this->isReadyForCall()){
			return false;
		}
		return $this->checkWeight()->__soapCall(__FUNCTION__, array_merge($this->clientOptions, $this->params));
	}

	protected function checkWeight(){
		if(isset($this->params['weight']) && !($this->params['weight'] > 0)){
			$this->params['weight'] = 0.01; // TODO Should this be defined from options?
		}
		return $this;
	}
}