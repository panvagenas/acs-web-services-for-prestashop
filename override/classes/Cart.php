<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 10/11/2014
 * Time: 12:14 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */
if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

class Cart extends CartCore {
	public function getDeliveryOptionList( Country $default_country = null, $flush = false ) {
		static $cache = null;
		if ($cache !== null && !$flush)
			return $cache;

		$delivery_option_list = array();
		$carriers_price = array();
		$carrier_collection = array();
		$package_list = $this->getPackageList();

		// Foreach addresses
		foreach ($package_list as $id_address => $packages)
		{
			// Initialize vars
			$delivery_option_list[$id_address] = array();
			$carriers_price[$id_address] = array();
			$common_carriers = null;
			$best_price_carriers = array();
			$best_grade_carriers = array();
			$carriers_instance = array();

			// Get country
			if ($id_address)
			{
				$address = new Address($id_address);
				$country = new Country($address->id_country);
			}
			else
				$country = $default_country;

			// Foreach packages, get the carriers with best price, best position and best grade
			foreach ($packages as $id_package => $package)
			{
				// No carriers available
				if (count($package['carrier_list']) == 1 && current($package['carrier_list']) == 0)
				{
					$cache = array();
					return $cache;
				}

				$carriers_price[$id_address][$id_package] = array();

				// Get all common carriers for each packages to the same address
				if (is_null($common_carriers))
					$common_carriers = $package['carrier_list'];
				else
					$common_carriers = array_intersect($common_carriers, $package['carrier_list']);

				$best_price = null;
				$best_price_carrier = null;
				$best_grade = null;
				$best_grade_carrier = null;

				// Foreach carriers of the package, calculate his price, check if it the best price, position and grade
				foreach ($package['carrier_list'] as $id_carrier)
				{
					if (!isset($carriers_instance[$id_carrier]))
						$carriers_instance[$id_carrier] = new Carrier($id_carrier);

					$price_with_tax = $this->getPackageShippingCost($id_carrier, true, $country, $package['product_list']);
					$price_without_tax = $this->getPackageShippingCost($id_carrier, false, $country, $package['product_list']);
					if (is_null($best_price) || $price_with_tax < $best_price)
					{
						$best_price = $price_with_tax;
						$best_price_carrier = $id_carrier;
					}
					$carriers_price[$id_address][$id_package][$id_carrier] = array(
						'without_tax' => $price_without_tax,
						'with_tax' => $price_with_tax);

					$grade = $carriers_instance[$id_carrier]->grade;
					if (is_null($best_grade) || $grade > $best_grade)
					{
						$best_grade = $grade;
						$best_grade_carrier = $id_carrier;
					}
				}

				$best_price_carriers[$id_package] = $best_price_carrier;
				$best_grade_carriers[$id_package] = $best_grade_carrier;
			}

			// Reset $best_price_carrier, it's now an array
			$best_price_carrier = array();
			$key = '';

			// Get the delivery option with the lower price
			foreach ($best_price_carriers as $id_package => $id_carrier)
			{
				$key .= $id_carrier.',';
				if (!isset($best_price_carrier[$id_carrier]))
					$best_price_carrier[$id_carrier] = array(
						'price_with_tax' => 0,
						'price_without_tax' => 0,
						'package_list' => array(),
						'product_list' => array(),
					);
				$best_price_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
				$best_price_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
				$best_price_carrier[$id_carrier]['package_list'][] = $id_package;
				$best_price_carrier[$id_carrier]['product_list'] = array_merge($best_price_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
				$best_price_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
			}

			// Add the delivery option with best price as best price
			$delivery_option_list[$id_address][$key] = array(
				'carrier_list' => $best_price_carrier,
				'is_best_price' => true,
				'is_best_grade' => false,
				'unique_carrier' => (count($best_price_carrier) <= 1)
			);

			// Reset $best_grade_carrier, it's now an array
			$best_grade_carrier = array();
			$key = '';

			// Get the delivery option with the best grade
			foreach ($best_grade_carriers as $id_package => $id_carrier)
			{
				$key .= $id_carrier.',';
				if (!isset($best_grade_carrier[$id_carrier]))
					$best_grade_carrier[$id_carrier] = array(
						'price_with_tax' => 0,
						'price_without_tax' => 0,
						'package_list' => array(),
						'product_list' => array(),
					);
				$best_grade_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
				$best_grade_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
				$best_grade_carrier[$id_carrier]['package_list'][] = $id_package;
				$best_grade_carrier[$id_carrier]['product_list'] = array_merge($best_grade_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
				$best_grade_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
			}

			// Add the delivery option with best grade as best grade
			if (!isset($delivery_option_list[$id_address][$key]))
				$delivery_option_list[$id_address][$key] = array(
					'carrier_list' => $best_grade_carrier,
					'is_best_price' => false,
					'unique_carrier' => (count($best_grade_carrier) <= 1)
				);
			$delivery_option_list[$id_address][$key]['is_best_grade'] = true;

			// Get all delivery options with a unique carrier
			foreach ($common_carriers as $id_carrier)
			{
				$key = '';
				$package_list = array();
				$product_list = array();
				$price_with_tax = 0;
				$price_without_tax = 0;

				foreach ($packages as $id_package => $package)
				{
					$key .= $id_carrier.',';
					$price_with_tax += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
					$price_without_tax += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
					$package_list[] = $id_package;
					$product_list = array_merge($product_list, $package['product_list']);
				}

				if (!isset($delivery_option_list[$id_address][$key]))
					$delivery_option_list[$id_address][$key] = array(
						'is_best_price' => false,
						'is_best_grade' => false,
						'unique_carrier' => true,
						'carrier_list' => array(
							$id_carrier => array(
								'price_with_tax' => $price_with_tax,
								'price_without_tax' => $price_without_tax,
								'instance' => $carriers_instance[$id_carrier],
								'package_list' => $package_list,
								'product_list' => $product_list,
							)
						)
					);
				else
					$delivery_option_list[$id_address][$key]['unique_carrier'] = (count($delivery_option_list[$id_address][$key]['carrier_list']) <= 1);
			}
		}

		$cart_rules = CartRule::getCustomerCartRules(Context::getContext()->cookie->id_lang, Context::getContext()->cookie->id_customer, true, true, false, $this);

		$free_carriers_rules = array();
		foreach ($cart_rules as $cart_rule)
		{
			if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction'])
			{
				$cr = new CartRule((int)$cart_rule['id_cart_rule']);
				if (Validate::isLoadedObject($cr))
				{
					$carriers = $cr->getAssociatedRestrictions('carrier', true, false);
					if (is_array($carriers) && count($carriers) && isset($carriers['selected']))
						foreach($carriers['selected'] as $carrier)
							if (isset($carrier['id_carrier']) && $carrier['id_carrier'])
								$free_carriers_rules[] = (int)$carrier['id_carrier'];
				}
			}
		}

		// For each delivery options :
		//    - Set the carrier list
		//    - Calculate the price
		//    - Calculate the average position
		foreach ($delivery_option_list as $id_address => $delivery_option)
			foreach ($delivery_option as $key => $value)
			{
				$total_price_with_tax = 0;
				$total_price_without_tax = 0;
				$position = 0;
				foreach ($value['carrier_list'] as $id_carrier => $data)
				{
					$total_price_with_tax += $data['price_with_tax'];
					d($data);
					$total_price_without_tax += $data['price_without_tax'];
					$total_price_without_tax_with_rules = (in_array($id_carrier, $free_carriers_rules)) ? 0 : $total_price_without_tax ;

					if (!isset($carrier_collection[$id_carrier]))
						$carrier_collection[$id_carrier] = new Carrier($id_carrier);
					$delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = $carrier_collection[$id_carrier];

					if (file_exists(_PS_SHIP_IMG_DIR_.$id_carrier.'.jpg'))
						$delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_.$id_carrier.'.jpg';
					else
						$delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = false;

					$position += $carrier_collection[$id_carrier]->position;
				}
				$delivery_option_list[$id_address][$key]['total_price_with_tax'] = $total_price_with_tax;
				$delivery_option_list[$id_address][$key]['total_price_without_tax'] = $total_price_without_tax;
				$delivery_option_list[$id_address][$key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;
				$delivery_option_list[$id_address][$key]['position'] = $position / count($value['carrier_list']);
			}

		// Sort delivery option list
		foreach ($delivery_option_list as &$array)
			uasort ($array, array('Cart', 'sortDeliveryOptionList'));

		$cache = $delivery_option_list;
		return $delivery_option_list;


		var_dump(parent::getDeliveryOptionList($default_country, $flush));die;

		$opts = \acsws\classes\ACSWSOptions::getInstance();
		$s    = new \acsws\classes\soap\ACSSoapPriceCalculation( array(
			'companyId'   => $opts->getValue( 'companyId' ),
			'companyPass' => $opts->getValue( 'companyPass' ),
			'username'    => $opts->getValue( 'username' ),
			'password'    => $opts->getValue( 'password' ),
			'customerId'  => $opts->getValue( 'customerId' ),
		) );

		$s->setParams( array(
			'st_from'        => 'ΑΘ',
			'st_to'          => 'ΑΘ',
			'varos'          => 10.00,
			'itemType'       => 1,
			'width'          => 50,
			'height'         => 50,
			'length'         => 50,
			'date_par'       => date( 'd/m/Y' ),
			'products'       => 'ΑΝ',
			'xrewsh'         => 2,
			'zone'           => '',
			'asf_poso'       => 0,
			'invoiceCountry' => 'GR',
			'lang'           => 'GR'
		) );

		var_dump( $s->getPriceNew() );
		die;
	}
}