<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 10/11/2014
 * Time: 2:42 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */
if (!defined('_PS_VERSION_'))
  exit;
  
class ACSWSOptions{
	protected $optionsArrayName = 'ACSWebServicesOptions';

//	protected $defaults = array(
//		'companyId' => '997942446',
//		'companyPass' => '8730',
//		'username' => 'info',
//		'password' => '1061',
//		'customerId' => '2ΒΣ60129',
//	);

	protected $defaults = array(
		'companyId' => 'demo',
		'companyPass' => 'demo',
		'username' => 'demo',
		'password' => 'demo',
		'customerId' => 'demo',
	);

	protected $stored = array();

	/**
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	protected function init() {
		$this->stored = unserialize( Configuration::get( $this->optionsArrayName ) );

		if ( ! $this->stored || empty( $this->stored ) ) {
			$this->stored = $this->defaults;
			$this->saveOptions( $this->stored );
		}

		$this->stored = array_merge($this->defaults, $this->stored);

		return $this;
	}

	/**
	 * @param $optionName
	 * @param bool $default
	 *
	 * @return mixed
	 * @throws Exception
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function getValue( $optionName, $default = false ) {
		if ( ! isset( $this->defaults[ $optionName ] ) ) {
			throw new Exception( 'No matching option' );
		}
		if ( $default ) {
			return $this->defaults[ $optionName ];
		}

		return isset( $this->stored[ $optionName ] ) ? $this->stored[ $optionName ] : $this->defaults[ $optionName ];
	}

	/**
	 * @param $newOptions
	 *
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function saveOptions( $newOptions ) {
		$this->stored = $this->validateOptions( $newOptions );

		return Configuration::updateValue( $this->optionsArrayName, serialize( $this->stored ) );
	}

	/**
	 * @param bool $defaults
	 *
	 * @return array
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function getOptionsArray( $defaults = false ) {
		return $defaults ? $this->defaults : $this->stored;
	}

	/**
	 *
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Call this method to get singleton
	 *
	 * @return ACSWSOptions
	 */
	public static function Instance() {
		static $inst = null;
		if ( $inst === null ) {
			$inst = new ACSWSOptions();
		}

		return $inst;
	}
}