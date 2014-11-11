<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 11/11/2014
 * Time: 10:20 πμ
 * Copyright: 2014 Panagiotis Vagenas
 */

namespace acsws\classes\soap;

if (!defined('_PS_VERSION_'))
  exit;

abstract class ACSSoap extends \SoapClient{
	protected $wsdl;
	protected $requiredParams = array();
	protected $params = array();

	public $clientOptions = array( /// TODO Remove these
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