<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 10/11/2014
 * Time: 12:16 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */
if (!defined('_PS_VERSION_'))
  exit;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'XDAutoLoader.php';

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
		$this->loader->addNamespace('\acsws\classes', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes');
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
		$form = new \XDaRk\Form();
		$form->init($this);
		return $form->addTextField($this->l('Company ID'), 'companyId', 'lg')
			->setFieldsValues($options->getOptionsArray())
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

		return $this->registerHook( 'DisplayHeader' );
	}

	public function hookDisplayHeader( $p ) {
//		$client = new SoapClient("https://services.acscourier.net/ACSPriceCalculation-portlet/axis/Plugin_ACSPriceCalculation_ACSPriceService?wsdl");
//
//		$companyId = "997942446"; // ID eterias - egatastashs
//		$companyPass = "8730"; // kwdikos prosvashs eterias
//		$username = "info"; // onoma xrhsth
//		$password = "1061"; // kwdikos prosvashs xrhsth
//		$customerId =  '2ΒΣ60129' ; // arithmos apostolhs
//		$st_from= 'ΑΘ';
//			$st_to= 'ΑΘ';
//			$varos= 10.00;
//			$itemType= 1;
//			$width= 50;
//			$height= 50;
//			$length= 50;
//			$date_par= date('d/m/Y');
//			$products= 'ΑΝ';
//			$xrewsh = 2;
//			$zone = '';
//			$asf_poso = 0;
//			$invoiceCountry = 'GR';
//			$lang = 'GR';
//
//		$result = $client->getPriceNew( $companyId,
//			$companyPass,
//			$username,
//			$password,
//			$customerId,
//			'ΑΘ','ΑΘ',10.00, 1, 50, 50, 50, $date_par, $products, $xrewsh, $zone, $asf_poso, $invoiceCountry, $lang
//		);
//
//		var_dump($result);die;

//		require_once dirname(__FILE__) . '/classes/acssoap.php';
//		require_once dirname(__FILE__) . '/classes/acsws.php';
//		require_once dirname(__FILE__) . '/classes/acswsoptions.php';
		$o = \XDaRk\Options::getInstance();

		$s = new acsws\classes\soap\ACSSoapAreaService(array(
			'companyId' => '997942446',
			'companyPass' => '8730',
			'username' => 'info',
			'password' => '1061',
		));
		$s->setParams(array(
			'zip_code' => '13341',
			'only_dp' => false
		));

		var_dump($s->findByZipCode());
		var_dump($s->getByZipCode());

//		$s = new ACSSoapPriceCalcultation(array(
//			'companyId' => '997942446',
//			'companyPass' => '8730',
//			'username' => 'info',
//			'password' => '1061',
//			'customerId' => '2ΒΣ60129',
//		));
//
//		$s->setParams(array(
//			'st_from' => 'ΑΘ',
//			'st_to' => 'ΑΘ',
//			'varos' => 10.00,
//			'itemType' => 1,
//			'width' => 50,
//			'height' => 50,
//			'length' => 50,
//			'date_par' => date('d/m/Y'),
//			'products' => 'ΑΝ',
//			'xrewsh' => 2,
//			'zone' => '',
//			'asf_poso' => 0,
//			'invoiceCountry' => 'GR',
//			'lang' => 'GR'
//		));
//
//		var_dump($s->getPriceNew());
		die;

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