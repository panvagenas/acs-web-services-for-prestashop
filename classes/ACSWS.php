<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 10/11/2014
 * Time: 12:54 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */
namespace acsws\classes;

use acsws\classes\soap\ACSSoapAddressService;
use acsws\classes\soap\ACSSoapAreaService;
use acsws\classes\soap\ACSSoapPriceCalculation;
use XDaRkOld\Singleton;
use XDaRkOld\TransLit;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

class ACSWS extends Singleton{
	/**
	 * @var ACSWSCache
	 */
	protected $cache;

	protected function __construct(){
		$this->cache = ACSWSCache::getInstance();
	}

	/**
	 * @param $from
	 * @param $to
	 * @param $weight
	 * @param bool $sendDate
	 * @param bool $service
	 * @param bool $charge
	 * @param string $zone
	 * @param int $insurance
	 *
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function getPrice( $from, $to, $weight, $sendDate = false, $service = false, $charge = false, $zone = '', $insurance = 0 ) {
		$sendDate  = $sendDate ? $sendDate : date('d/m/Y');
		$service = $service ? $service : Defines::$_prod_PortaPorta;
		$charge = $charge ? $charge : Defines::$_xreosi_apostolea;

		$params = array(
			'st_from'        => $from,
			'st_to'          => $to,
			'varos'          => $weight,
			'date_par'       => $sendDate,
			'products'       => $service,
			'xrewsh'         => $charge,
			'zone'           => $zone,
			'asf_poso'       => $insurance,
		);

		$call = $this->cache->hasCall(new ACSWSCall(__METHOD__, $params, null));
		if($call){
			return $call->result;
		}

		$soap = new ACSSoapPriceCalculation( ACSWSOptions::getInstance()->getCustomerOptions() );
		$soap->setParams($params);

		$res = $soap->getPrice();

		$price = false;
		if(is_object($res) && isset($res->price)){
			$price = $res->price;
			$this->cache->storeCall(new ACSWSCall(__METHOD__, $params, $res->price));
		}

		return $price;
	}

	/**
	 * @param array $address
	 * @param string $lang
	 *
	 * @return bool|mixed
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function validateAddress(Array $address, $lang = 'GR'){
		$customerOptions =  ACSWSOptions::getInstance()->getCustomerOptions();
		unset($customerOptions['customerId']);

		$loc = TransLit::getInstance()->translate($this->addressArrayToLocation($address));

		$params = array(
			'lang' => $lang,
			'address' => $loc
		);

		$call = $this->cache->hasCall(new ACSWSCall(__METHOD__, $params, null));

		if($call){
			if(!$call->result && isset($address['area'])) {
				unset($address['area']);
				return $this->validateAddress($address);
			}
			return $call->result;
		}

		$soap = new ACSSoapAddressService($customerOptions);
		$soap->setParams($params);

		$res = $soap->validateAddress();
		$resultObj = isset($res[0]) ? $res[0] : null;
		if((!is_object($resultObj) || !isset($resultObj->station_id) || empty($resultObj->station_id)) && isset($address['area'])){
			unset($address['area']);
			return $this->validateAddress($address);
		}

		$this->cache->storeCall(new ACSWSCall(__METHOD__, $params, $res));

		return $res;
	}

	/**
	 * @param $zip
	 * @param bool $dpOnly
	 *
	 * @return bool|mixed
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function findByZipCode($zip, $dpOnly = false){
		$customerOptions =  ACSWSOptions::getInstance()->getCustomerOptions();
		unset($customerOptions['customerId']);

		$params = array(
			'zip_code' => $zip,
			'only_dp' => $dpOnly
		);

		$call = $this->cache->hasCall(new ACSWSCall(__METHOD__, $params, null));
		if($call){
			return $call->result;
		}

		$soap = new ACSSoapAreaService($customerOptions);
		$soap->setParams($params);

		$res = $soap->findByZipCode();

		$this->cache->storeCall(new ACSWSCall(__METHOD__, $params, $res));

		return $res;
	}

	public function isDisprosito(Array $address, $lang = 'GR'){
		$res = $this->validateAddress($address, $lang);
		$res = isset($res[0]) ? $res[0] : null;
		return is_object($res) && isset($res->dp_dx) && !empty($res->dp_dx) && in_array($res->dp_dx, Defines::$_disprosito_array);
	}

	/**
	 * @param array $address
	 * @param string $lang
	 *
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function getStationIdFromAddress(Array $address, $lang = 'GR'){
		$res = $this->validateAddress($address, $lang);
		$res = isset($res[0]) ? $res[0] : null;
		return is_object($res) && isset($res->station_id) ? $res->station_id : false;
	}

	/**
	 * @param array $addressArray
	 *
	 * @return string
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	protected function addressArrayToLocation(Array $addressArray){
		$loc = '';
		$loc .= isset($addressArray['street']) ? $addressArray['street'] . ' ' : '';
		$loc .= isset($addressArray['number']) ? $addressArray['number'] . ', ' : ', ';
		$loc .= isset($addressArray['pc']) ? $addressArray['pc'] . ' ' : ' ';
		$loc .= isset($addressArray['area']) ? $addressArray['area'] : '';
		return $loc;
	}
}