<?php
/**
 * acswebservices
 * ACSWSCall.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 12/11/2014
 * Time: 4:02 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */

namespace acsws\classes;


class ACSWSCall {
	public $method;
	public $params;
	public $result;

	public function __construct($method, $params, $result){
		$this->method = $method;
		$this->params = $params;
		$this->result = $result;
	}
} 