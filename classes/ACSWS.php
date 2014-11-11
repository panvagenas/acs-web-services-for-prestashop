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

if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

class ACSWS {
	public function getPrice( $from, $to, $weight, $width = 0, $height = 0, $length = 0, $sendDate = false, $service = false, $charge = false, $zone = '', $insurance = 0, $invoiceCountry = 'GR', $lang = 'GR' ) {
		$sendDate  = $sendDate ? $sendDate : date('d/m/Y');
		$service = $service ? $service : Defines::$_prod_PortaPorta;
		$charge = $charge ? $charge : Defines::$_xreosi_apostolea;

		$soap = new ACSSoapPriceCalculation( ACSWSOptions::getInstance()->getCustomerOptions() );
		$soap->setParams( array(
			'st_from'        => $from,
			'st_to'          => $to,
			'varos'          => $weight,
			'itemType'       => '',
			'width'          => $width,
			'height'         => $height,
			'length'         => $length,
			'date_par'       => $sendDate,
			'products'       => $service,
			'xrewsh'         => $charge,
			'zone'           => $zone,
			'asf_poso'       => $insurance,
			'invoiceCountry' => $invoiceCountry,
			'lang'           => $lang
		) );
		return $soap->getPriceNew();
	}

	public function validateAddress(Array $address, $lang = 'GR'){
		$customerOptions =  ACSWSOptions::getInstance()->getCustomerOptions();
		unset($customerOptions['customerId']);
		$loc = '';
		$loc .= isset($address['street']) ? $address['street'] . ' ' : '';
		$loc .= isset($address['number']) ? $address['number'] . ', ' : ', ';
		$loc .= isset($address['pc']) ? $address['pc'] . ' ' : ' ';
		$loc .= isset($address['area']) ? $address['area'] : '';

		$soap = new ACSSoapAddressService($customerOptions);
		$soap->setParams(array(
			'lang' => $lang,
			'address' => $loc
		));

		return $soap->validateAddress();
	}

	public function findByZipCode($zip, $dpOnly = false){
		$customerOptions =  ACSWSOptions::getInstance()->getCustomerOptions();
		unset($customerOptions['customerId']);

		$soap = new ACSSoapAreaService($customerOptions);
		$soap->setParams(array(
			'zip_code' => $zip,
			'only_dp' => $dpOnly
		));

		return $soap->findByZipCode();
	}
}