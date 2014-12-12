<?php
/**
 * acswebservices
 * ACSWSCache.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 12/11/2014
 * Time: 3:58 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */

namespace acsws\classes;

use XDaRkOld\Singleton;

if (!defined('_PS_VERSION_'))
	exit;

class ACSWSCache extends Singleton{
	protected $cache = array();

	public function storeCall(ACSWSCall $call){
		$this->cache[] = $call;
	}

	/**
	 * @param ACSWSCall $call
	 *
	 * @return bool|ACSWSCall
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function hasCall(ACSWSCall $call){
		foreach ( $this->cache as $c ) {
			if($call->method == $c->method && $call->params == $c->params){
				return $c;
			}
		}
		return false;
	}

	public function getCall(ACSWSCall $call){
		return $this->hasCall($call);
	}

	public function deleteCall(ACSWSCall $call){
		foreach ( $this->cache as $k => $c ) {
			if($call->method == $c->method && $call->params == $c->params){
				unset($this->cache[$k]);
				return true;
			}
		}
		return false;
	}
} 