<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 10/11/2014
 * Time: 2:42 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */

namespace acsws\classes;

use XDaRk\Options;

if (!defined('_PS_VERSION_'))
  exit;
  
class ACSWSOptions extends Options{
	/**
	 * @var string
	 */
	protected $optionsArrayName = 'ACSWebServicesOptions';
	/**
	 * @var array
	 */
	protected $defaults = array(
		'companyId' => 'demo',
		'companyPass' => 'demo',
		'username' => 'demo',
		'password' => 'demo',
		'customerId' => 'demo',
	);

	/**
	 * @param array $newOptions
	 *
	 * @return array
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	protected function validateOptions(Array $newOptions){
		$newOptions['companyId'] = isset($newOptions['companyId']) ? (string)$newOptions['companyId'] : $this->defaults['companyId'];
		$newOptions['companyPass'] = isset($newOptions['companyPass']) ? (string)$newOptions['companyPass'] : $this->defaults['companyPass'];
		$newOptions['username'] = isset($newOptions['username']) ? (string)$newOptions['username'] : $this->defaults['username'];
		$newOptions['password'] = isset($newOptions['password']) ? (string)$newOptions['password'] : $this->defaults['password'];
		$newOptions['customerId'] = isset($newOptions['customerId']) ? (string)$newOptions['customerId'] : $this->defaults['customerId'];

		return parent::validateOptions($newOptions);
	}

	public function getCustomerOptions(){
		return array(
			'companyId'   => $this->getValue( 'companyId' ),
			'companyPass' => $this->getValue( 'companyPass' ),
			'username'    => $this->getValue( 'username' ),
			'password'    => $this->getValue( 'password' ),
			'customerId'  => $this->getValue( 'customerId' ),
		);
	}
}