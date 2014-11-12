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
	/**
	 * Return package shipping cost
	 *
	 * @param integer $id_carrier Carrier ID (default : current carrier)
	 * @param boolean $use_tax
	 * @param Country $default_country
	 * @param array $product_list List of product concerned by the shipping. If null, all the product of the cart are used to calculate the shipping cost
	 *
	 * @param null $id_zone
	 *
	 * @throws PrestaShopException
	 * @return float Shipping total
	 */
	public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null)
	{
		if ($this->isVirtualCart())
			return 0;

		$shipping_cost = Hook::exec('packageShippingCost', get_defined_vars() + array('cart' => $this));

		if(is_numeric($shipping_cost)){
			return $shipping_cost;
		}

		return parent::getPackageShippingCost($id_carrier, $use_tax, $default_country, $product_list, $id_zone);
	}
}