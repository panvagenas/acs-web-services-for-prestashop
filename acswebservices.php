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

class ACSWebServices extends Module {
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
		if ( parent::install() == false ) {
			return false;
		}

		return $this->registerHook( 'DisplayCarrierList' ) && $this->registerHook( 'packageShippingCost' );
	}

	public function hookPackageShippingCost( $p ) {
		$carrier = new Carrier( (int) $p['id_carrier'] );

		if ( ! $carrier || $carrier->name !== 'ACS Courier' ) {
			return false;
		}

		/* @var Cart $cart */
		$cart   = $p['cart'];
		$weight = 0;
		$volume = 0;

		/* @var Product $product */
		foreach ( $cart->getProducts() as $product ) {
			$weight += $product['weight'];
			if ( is_numeric( $product['width'] ) && is_numeric( $product['height'] ) && is_numeric( $product['depth'] ) ) {
				$volume += ( $product['width'] * $product['height'] * $product['depth'] ) / 5000;
			}
		}

		$addressObj = new Address( $cart->id_address_delivery );

		$address = array(
			'street' => $addressObj->address1 . ( $addressObj->address2 ? $addressObj->address2 : '' ),
			'number' => null,
			'pc'     => $addressObj->postcode,
			'area'   => $addressObj->city,
		);


		$soap = \acsws\classes\ACSWS::getInstance();

		$dp = $soap->isDisprosito($address) ? \acsws\classes\Defines::$_prod_Disprosita : false;

		$stationId = $soap->getStationIdFromAddress( $address );

		$price = $soap->getPrice( 'ΑΘ', $stationId, max( $weight, $volume ), false, $dp );

		return $price;
	}

	public function hookDisplayCarrierList( $p ) {
		/* @var AddressCore $address */
		$address = &$p['address'];

		/* @var CookieCore $cookie */
		$cookie = $p['cookie'];

		/* @var Cart $cart */
		$cart = $p['cart'];

//		d($p);

		return "<div><h1>new Carrier</h1></div>";
//		$loc1 = array(
//			'street' => 'Καλλινίκου',
//			'number' => '48',
//			'pc' => '13341',
//			'area' => 'Άνω Λιόσια'
//		);
//		$loc2 = array(
//			'street' => 'Αλμύρα',
//			'number' => '',
//			'pc' => '20300',
//			'area' => 'Αλμύρα'
//		);
//		$s = new \acsws\classes\ACSWS();
//		$res = $s->validateAddress($loc1);
//		var_dump($res);
//		$res = $s->validateAddress($loc2);
//		var_dump($res);

//		$res = $s->getPrice('ΑΘ', 'ΚΟ', 1.5);
//		var_dump($res);
//
//		die(__METHOD__);
	}

	/**
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function uninstall() {
		if ( ! parent::uninstall() ) {
			return false;
		}

		return true;
	}
}