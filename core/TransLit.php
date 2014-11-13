<?php
/**
 * acswebservices
 * Translit.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 12/11/2014
 * Time: 12:37 μμ
 * Copyright: 2014 Panagiotis Vagenas
 */

namespace XDaRk;


class TransLit extends Singleton{
	protected function __construct(){
		parent::__construct();
		$this->en_el = array_merge(array_flip($this->el_en), $this->en_el);
	}

	public function translate($string){
		$ar = str_split((string)$string);
		foreach ( $ar as $k => $ch ) {
			if(is_numeric($ch) || !isset($this->en_el[$ch])) continue;

			$ar[$k] = $this->en_el[$ch];
		}
		return implode('', $ar);
	}

	public function translateElEn($string){
		$ar = preg_split('//u',(string)$string, -1, PREG_SPLIT_NO_EMPTY);
		foreach ( $ar as $k => $ch ) {
			if(is_numeric($ch) || !isset($this->el_en[$ch])) continue;

			$ar[$k] = $this->el_en[$ch];
		}
		return implode('', $ar);
	}

	protected $en_el = array(
		'S' => 'Σ',
		's' => 'σ'
	);
	protected $el_en = array (
		' ' => ' ',
// upper case
		'Α' => 'A',
		'Ά' => 'Á',
		'Β' => 'V',
		'Γ' => 'G',
		'Δ' => 'D',
		'Ε' => 'E',
		'Έ' => 'É',
		'Ζ' => 'Z',
		'Η' => 'I',
		'Ή' => 'I',
		'Θ' => 'Th',
		'Ι' => 'I',
		'Ί' => 'I',
		'Ϊ' => 'I',
		'ΐ' => 'i',
		'Κ' => 'K',
		'Λ' => 'L',
		'Μ' => 'M',
		'Ν' => 'N',
		'Ξ' => 'X',
		'Ο' => 'O',
		'Ό' => 'Ó',
		'Π' => 'P',
		'Ρ' => 'R',
		'Σ' => 'S',
		'Τ' => 'T',
		'Υ' => 'Y',
		'Ύ' => 'Y',
		'Ϋ' => 'Y',
		'ΰ' => 'Y',
		'Φ' => 'F',
		'Χ' => 'Ch',
		'Ψ' => 'Ps',
		'Ω' => 'O',
		'Ώ' => 'O',
// lower case
		'ς' => 's',
		'α' => 'a',
		'ά' => 'á',
		'β' => 'v',
		'γ' => 'g',
		'δ' => 'd',
		'ε' => 'e',
		'έ' => 'é',
		'ζ' => 'z',
		'η' => 'ī',
		'ή' => 'ī́',
		'θ' => 'th',
		'ι' => 'i',
		'ί' => 'í',
		'ϊ' => 'ï',
		'κ' => 'k',
		'λ' => 'l',
		'μ' => 'm',
		'ν' => 'n',
		'ξ' => 'x',
		'ο' => 'o',
		'ό' => 'ó',
		'π' => 'p',
		'ρ' => 'r',
		'σ' => 's',
		'τ' => 't',
		'υ' => 'y',
		'ύ' => 'ý',
		'ϋ' => 'ÿ',
		'φ' => 'f',
		'χ' => 'ch',
		'ψ' => 'ps',
		'ω' => 'ō',
		'ώ' => 'ṓ',
	);
} 