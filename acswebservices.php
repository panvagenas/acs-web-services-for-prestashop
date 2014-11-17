<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 10/11/2014
 * Time: 12:16 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */
if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'XDAutoLoader.php';

//$loader = new \XDaRk\XDAutoLoader();
//$loader->register();
//$loader->addNamespace('\acsws\classes', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes');

class ACSWebServices extends CarrierModule {
	public $id_carrier;
	/**
	 * @var string Name of this plugin
	 */
	public $name = 'acswebservices';
	/**
	 * @var string Description
	 */
	public $description = 'Calculates shipping costs from ACS Web Services';
	/**
	 * @var string
	 */
	public $tab = 'shipping_logistics';
	/**
	 * @var string
	 */
	public $version = '0.1';
	/**
	 * @var string
	 */
	public $author = 'Panagiotis Vagenas <pan.vagenas@gmail.com>';
	/**
	 * @var int
	 */
	public $need_instance = 0;
	/**
	 * @var array
	 */
	public $ps_versions_compliancy = array( 'min' => '1.5' );
	/**
	 * @var array
	 */
	public $dependencies = array();
	/**
	 * @var string
	 */
	public $displayName = 'ACS Web Services';

	/**
	 * @var \XDaRk\XDAutoLoader
	 */
	protected $loader;

	public $bootstrap = true;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();

		$this->displayName = $this->l( $this->displayName );
		$this->description = $this->l( $this->description );

		$this->confirmUninstall = $this->l( 'Are you sure you want to uninstall?' );

		$this->loader = new \XDaRk\XDAutoLoader();
		$this->loader->register();
		$this->loader->addNamespace( '\acsws\classes', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes' );
	}

	/**
	 * Module options page
	 *
	 * @return string
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function getContent() {
		$output = null;

		if ( Tools::isSubmit( 'submit' . $this->name ) ) {
			$newOptions = $_POST;

			$opts = \acsws\classes\ACSWSOptions::getInstance();
			if ( $opts->saveOptions( $newOptions ) ) {
				$output .= $this->displayConfirmation( $this->l( 'Settings updated' ) );
			} else {
				$output .= $this->displayError( $this->l( 'There was an error saving options' ) );
			}
		}

		return $output . $this->displayForm();
	}

	/**
	 * @return string
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function displayForm() {
		$options = \acsws\classes\ACSWSOptions::getInstance();
		$form    = new \XDaRk\Form();
		$form->init( $this );

		return $form->addTextField( $this->l( 'Company ID' ), 'companyId', 'lg' )
		            ->addTextField( $this->l( 'Company Password' ), 'companyPass', 'lg' )
		            ->addTextField( $this->l( 'Username' ), 'username', 'lg' )
		            ->addTextField( $this->l( 'User Password' ), 'password', 'lg' )
		            ->addTextField( $this->l( 'Customer ID' ), 'customerId', 'lg' )
		            ->setFieldsValues( $options->getOptionsArray() )
		            ->generateForm();
	}

	/**
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function install() {
		if ( ! parent::install() ) {
			return false;
		}
		if ( ! $this->registerHook( 'updateCarrier' ) OR
		     ! $this->registerHook( 'displayCarrierList' ) OR
		     ! $this->registerHook( 'displayAdminOrder' )
		) {
			return false;
		}
		return $this->installCarriers();
	}

	/**
	 * @param $params
	 * @param $shipping_cost
	 *
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function getOrderShippingCost( $params, $shipping_cost ) {
		return $this->getOrderShippingCostExternal( $params );
	}

	/**
	 * @param $params
	 *
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function getOrderShippingCostExternal( $params ) {
		if ( $this->id_carrier == Configuration::get('ACS_CLDE') ) {
			return $this->packageShippingCost($params, false);
		} elseif ($this->id_carrier == Configuration::get('ACS_DP')){
			$dp = $this->packageShippingCost($params, true);
			if(is_numeric($dp)){
				return $dp;
			}
		}
		return false;
	}

	/**
	 * @return string
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function hookDisplayCarrierList($params) {
		$cart = $params['cart'];
		$dp = $this->packageShippingCost($cart, true);
		if(!$dp){
			return '';
		}
		$addressObj = &$params['address'];
		$soap = \acsws\classes\ACSWS::getInstance();
		$storeInfo = $soap->validateAddress(array(
			'street' => $addressObj->address1 . ( $addressObj->address2 ? $addressObj->address2 : '' ),
			'number' => null,
			'pc'     => $addressObj->postcode,
			'area'   => $addressObj->city,
		));

		if(!isset($storeInfo[0])){
			return '';
		}

//		var_dump($storeInfo);die;
		return '
		<script type="text/javascript">
			var storeInfo = '.json_encode($this->object_to_array($storeInfo[0])).';
			var dpCarrierId = '.Configuration::get('ACS_DP').';
			var carrierId = '.Configuration::get('ACS_CLDE').';
			var googleQ = "'.\XDaRk\TransLit::getInstance()->translateElEn($storeInfo[0]->station_address.', '.$storeInfo[0]->station_description).'";
			console.log(storeInfo);
			'.file_get_contents(dirname(__FILE__).'/assets/dp.js').'
		</script>
		';
	}

	public function object_to_array($data)
	{
		if(is_array($data) || is_object($data))
		{
			$result = array();

			foreach($data as $key => $value) {
				$result[$key] = $this->object_to_array($value);
			}

			return $result;
		}

		return $data;
	}

	/**
	 * @return string
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function hookDisplayAdminOrder() {
		return '';
	}

	/**
	 * @param $params
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function hookUpdateCarrier( $params ) {
		$old_id_carrier = (int)$params['id_carrier'];
		$new_id_carrier = (int)$params['carrier']->id;
		if (Configuration::get('ACS_CLDE') == $old_id_carrier)
			Configuration::updateValue('ACS_CLDE', $new_id_carrier);
		if (Configuration::get('ACS_DP') == $old_id_carrier)
			Configuration::updateValue('ACS_DP', $new_id_carrier);
	}

	/**
	 * @param $params
	 *
	 * @return string
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function hookExtraCarrier( $params ) {
		return '';
	}

	/**
	 * @param Cart $cart
	 * @param $dp
	 *
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function packageShippingCost(Cart $cart, $dp ) {
		$addressObj = new Address( $cart->id_address_delivery );

		if($addressObj->country != 'Greece' && $addressObj->country != 'Ελλάδα'){
			return false;
		}

		$weight = 0;
		$volume = 0;

		/* @var Product $product */
		foreach ( $cart->getProducts() as $product ) {
			$weight += $product['weight'] > 0 ? $product['weight'] : 0.1;
			if ( is_numeric( $product['width'] ) && is_numeric( $product['height'] ) && is_numeric( $product['depth'] ) ) {
				$value = ( $product['width'] * $product['height'] * $product['depth'] ) / 5000;
				$volume += $value > 0 ? $value : 0.1;
			}
		}

		$address = array(
			'street' => $addressObj->address1 . ( $addressObj->address2 ? $addressObj->address2 : '' ),
			'number' => null,
			'pc'     => $addressObj->postcode,
			'area'   => $addressObj->city,
		);


		$soap = \acsws\classes\ACSWS::getInstance();

		if($dp && !$soap->isDisprosito( $address )){
			return false;
		}

		$stationId = $soap->getStationIdFromAddress( $address );

		if(!$stationId){
			return false;
		}

		$price = $soap->getPrice( 'ΑΘ', $stationId, max( $weight, $volume ), false, $dp ? \acsws\classes\Defines::$_prod_Disprosita : false );

		return $price;
	}

	/**
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function uninstall() {
		return parent::uninstall() && \acsws\classes\ACSWSOptions::getInstance()->deleteAllOptions();
	}

	/**
	 * @return bool
	 * @throws Exception
	 * @throws PrestaShopDatabaseException
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function installCarriers() {
		foreach ( \acsws\classes\ACSWSOptions::getInstance()->getValue('carrierList') as $carrier_key => $carrier_name ) {
			$carrierId = Configuration::get( $carrier_key );
			if ( $carrierId < 1 ) {
				// Create carrier
				$carrier                     = new Carrier();
				$carrier->name               = $carrier_name;
				$carrier->id_tax_rules_group = 0;
				$carrier->active             = 1;
				$carrier->deleted            = 0;
				foreach ( Language::getLanguages( true ) as $language ) {
					// TODO Carrier delay
					$carrier->delay[ (int) $language['id_lang'] ] = '' . $carrier_name;
				}
				$carrier->shipping_handling    = 1;
				$carrier->range_behavior       = 0;
				$carrier->is_module            = 1;
				$carrier->shipping_external    = 1;
				$carrier->external_module_name = $this->name;
				$carrier->need_range           = 1;
				if ( ! $carrier->add() ) {
					return false;
				}
				// Associate carrier to all groups
				$groups = Group::getGroups( true );
				foreach ( $groups as $group ) {
					Db::getInstance()->insert( 'carrier_group', array(
							'id_carrier' => (int) $carrier->id,
							'id_group'   => (int) $group['id_group']
						) );
				}
				// Create price range
				$rangePrice             = new RangePrice();
				$rangePrice->id_carrier = $carrier->id;
				$rangePrice->delimiter1 = '0';
				$rangePrice->delimiter2 = '10000';
				$rangePrice->add();
				// Create weight range
				$rangeWeight             = new RangeWeight();
				$rangeWeight->id_carrier = $carrier->id;
				$rangeWeight->delimiter1 = '0';
				$rangeWeight->delimiter2 = '10000';
				$rangeWeight->add();
				// Associate carrier to all zones
				$zones = Zone::getZones( true );
				foreach ( $zones as $zone ) {
					Db::getInstance()->insert( 'carrier_zone', array(
							'id_carrier' => (int) $carrier->id,
							'id_zone'    => (int) $zone['id_zone']
						) );
					Db::getInstance()->insert( 'delivery', array(
							'id_carrier'      => (int) $carrier->id,
							'id_range_price'  => (int) $rangePrice->id,
							'id_range_weight' => null,
							'id_zone'         => (int) $zone['id_zone'],
							'price'           => '0'
						) );
					Db::getInstance()->insert( 'delivery', array(
							'id_carrier'      => (int) $carrier->id,
							'id_range_price'  => null,
							'id_range_weight' => (int) $rangeWeight->id,
							'id_zone'         => (int) $zone['id_zone'],
							'price'           => '0'
						) );
				}
				copy( dirname( __FILE__ ) . '/img/logo.png', _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg' );
				Configuration::updateValue( $carrier_key, $carrier->id );
			}
		}

		return true;
	}
}