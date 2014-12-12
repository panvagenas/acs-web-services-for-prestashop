<?php
/**
 * acswebservices
 * ${FILE_NAME}
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 11/11/2014
 * Time: 10:29 πμ
 * Copyright: 2014 Panagiotis Vagenas
 */

namespace XDaRkOld;


if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

class Form extends \HelperFormCore {
	protected $default_lang;
	protected $initialized = false;
	protected $tab = 0;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * @param $label
	 * @param $name
	 * @param string $class
	 * @param bool $required
	 * @param bool $hint
	 * @param bool $description
	 *
	 * @param bool $prefix
	 * @param bool $suffix
	 *
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function addTextField( $label, $name, $class = 'lg', $required = true, $hint = false, $description = false, $prefix = false, $suffix = false, $type = 'text' ) {
		$f = array(
			'type'     => $type,
			'label'    => $label,
			'name'     => $name,
			'class'    => $class,
			'required' => $required,
		);
		if ( $hint !== false ) {
			$f['hint'] = $hint;
		}
		if ( $description !== false ) {
			$f['description'] = $description;
		}
		if ( $prefix !== false ) {
			$f['prefix'] = $prefix;
		}
		if ( $suffix !== false ) {
			$f['suffix'] = $suffix;
		}
		$this->addField( $f );

		return $this;
	}

	public function addPasswordField( $label, $name, $class = 'lg', $required = true, $hint = false, $description = false, $prefix = false, $suffix = false ){
		return $this->addTextField($label, $name, $class, $required, $hint, $description, $prefix, $suffix, 'password');
	}

	/**
	 * @param $label
	 * @param $name
	 * @param string $class
	 * @param array $options
	 * @param bool $required
	 * @param bool $hint
	 * @param bool $description
	 * @param string $optionId
	 * @param string $optionName
	 *
	 * @param bool $prefix
	 * @param bool $suffix
	 *
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function addSelectField( $label, $name, $class = 'lg', Array $options, $required = true, $hint = false, $description = false, $optionId = 'id_option', $optionName = 'name', $prefix = false, $suffix = false ) {
		$f = array(
			'type'     => 'select',
			'label'    => $label,
			'name'     => $name,
			'class'    => $class,
			'required' => $required,
			'options'  => array(
				'query' => $options,
				'id'    => $optionId,
				'name'  => $optionName
			)
		);
		if ( $hint !== false ) {
			$f['hint'] = $hint;
		}
		if ( $description !== false ) {
			$f['description'] = $description;
		}
		if ( $prefix !== false ) {
			$f['prefix'] = $prefix;
		}
		if ( $suffix !== false ) {
			$f['suffix'] = $suffix;
		}
		$this->addField( $f );

		return $this;
	}

	/**
	 * @param $label
	 * @param $name
	 * @param string $class
	 * @param array $options
	 * @param bool $required
	 * @param bool $hint
	 * @param bool $description
	 * @param string $optionId
	 * @param string $optionName
	 *
	 * @param bool $prefix
	 * @param bool $suffix
	 *
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function addMultiSelectField( $label, $name, $class = 'lg', Array $options, $required = false, $hint = false, $description = false, $optionId = 'id_option', $optionName = 'name', $prefix = false, $suffix = false ) {
		$f = array(
			'type'     => 'select',
			'label'    => $label,
			'name'     => $name . '[]',
			'class'    => $class,
			'required' => $required,
			'multiple' => true,
			'options'  => array(
				'query' => $options,
				'id'    => $optionId,
				'name'  => $optionName
			)
		);
		if ( $hint !== false ) {
			$f['hint'] = $hint;
		}
		if ( $description !== false ) {
			$f['description'] = $description;
		}
		if ( $prefix !== false ) {
			$f['prefix'] = $prefix;
		}
		if ( $suffix !== false ) {
			$f['suffix'] = $suffix;
		}
		$this->addField( $f );

		return $this;
	}

	/**
	 * @param $label
	 * @param $name
	 * @param string $class
	 * @param array $values
	 * @param bool $required
	 * @param bool $isBool
	 * @param bool $hint
	 * @param bool $description
	 *
	 * @param bool $prefix
	 * @param bool $suffix
	 *
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function addSwitchField( $label, $name, $class = 'lg', Array $values, $required = true, $isBool = true, $hint = false, $description = false, $prefix = false, $suffix = false ) {
		$f = array(
			'type'     => 'select',
			'label'    => $label,
			'name'     => $name,
			'class'    => $class,
			'required' => $required,
			'is_bool'  => $isBool,
			'values'   => $values
		);
		if ( $hint !== false ) {
			$f['hint'] = $hint;
		}
		if ( $description !== false ) {
			$f['description'] = $description;
		}
		if ( $prefix !== false ) {
			$f['prefix'] = $prefix;
		}
		if ( $suffix !== false ) {
			$f['suffix'] = $suffix;
		}
		$this->addField( $f );

		return $this;
	}

	/**
	 * @param array $fieldValues
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function setFieldsValues( Array $fieldValues ) {
		foreach ( $this->fields_form as $k => $f ) {
			if ( isset( $f['form']['input'] ) ) {
				foreach ( $f['form']['input'] as $ki => $fi ) {
					if ( $this->isMultiSelectField( $fi ) && isset( $fieldValues[ rtrim( $fi['name'], '[]' ) ] ) ) {
						$this->fields_value[ $fi['name'] ] = $fieldValues[ rtrim( $fi['name'], '[]' ) ];
						unset ( $fieldValues[ rtrim( $fi['name'], '[]' ) ] );
					} elseif(isset($fieldValues[$fi['name']])){
						$this->fields_value[ $fi['name'] ] = $fieldValues[$fi['name']];
					}
				}
			}
		}

		array_merge( $this->fields_value, $fieldValues );

		return $this;
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	protected function isMultiSelectField( $field ) {
		return is_array( $field ) && isset( $field['multiple'] ) && $field['multiple'] === true;
	}

	/**
	 * @param $index
	 * @param $title
	 * @param $image
	 * @param string $submitTitle
	 * @param string $submitClass
	 *
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function setTab( $index, $title, $image, $submitTitle = 'Save', $submitClass = 'button pull-right' ) {
		$this->tab                                     = $index;
		$this->fields_form[ $index ]['form']['legend'] = array( 'title' => $title);
		if ( $image ) {
			$this->fields_form[ $index ]['form']['legend']['image'] = $image;
		}
		$this->fields_form[ $index ]['form']['submit'] = array(
			'title' => $submitTitle,
			'class' => $submitClass
		);

		return $this;
	}

	/**
	 * @param array $fields_form
	 * @param array $fields_value
	 * @param \Module $module
	 *
	 * @return string
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function generateForm( $fields_form = array(), $fields_value = array(), \Module $module = null ) {
		if ( ( empty( $fields_form ) || empty( $fields_value ) || empty( $module ) ) && $this->initialized ) {
			return parent::generate();
		} else {
			$form = new Form( $module );
			$form->init( $module );
			$this->setFieldsForm( $fields_form )->setFieldsValue( $fields_value );

			return $form->generate();
		}
	}

	/**
	 * @param $field
	 *
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function addField( $field ) {
		if (!isset($this->fields_form[ $this->tab ]) || ! is_array( $this->fields_form[ $this->tab ] ) ) {
			$this->fields_form[ $this->tab ] = array();
		}
		if (!isset($this->fields_form[ $this->tab ]['form']) || ! is_array( $this->fields_form[ $this->tab ]['form'] ) ) {
			$this->fields_form[ $this->tab ]['form'] = array();
		}
		if (!isset($this->fields_form[ $this->tab ]['form']['input']) || ! is_array( $this->fields_form[ $this->tab ]['form']['input'] ) ) {
			$this->fields_form[ $this->tab ]['form']['input'] = array();
		}
		array_push( $this->fields_form[ $this->tab ]['form']['input'], $field );

		return $this;
	}

	/**
	 * @param array $fieldsForm
	 *
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function setFieldsForm( Array $fieldsForm ) {
		$this->fields_form = $fieldsForm;

		return $this;
	}

	/**
	 * @param $module
	 * @param int $startTab
	 * @param string $tabTitle
	 * @param bool $tabImage
	 * @param bool $bootstrap
	 * @param bool $title
	 * @param bool $showToolbar
	 * @param bool $toolBarScroll
	 * @param array $toolbarBtn
	 *
	 * @return $this
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since ${VERSION}
	 */
	public function init( $module, $startTab = 0, $tabTitle = 'General Options', $tabImage = false, $bootstrap = true, $title = false, $showToolbar = true, $toolBarScroll = true, $toolbarBtn = array() ) {
		$this->default_lang = (int) \Configuration::get( 'PS_LANG_DEFAULT' );

		$this->module          = $module;
		$this->name_controller = $module->name;
		$this->token           = \Tools::getAdminTokenLite( 'AdminModules' );
		$this->currentIndex    = \AdminController::$currentIndex . '&configure=' . $module->name;
		$this->bootstrap       = $bootstrap;

		// Language
		$this->default_form_language    = $this->default_lang;
		$this->allow_employee_form_lang = $this->default_lang;

		// Title and toolbar
		$this->title          = $title === false ? $module->displayName : $title;
		$this->show_toolbar   = $showToolbar;        // false -> remove toolbar
		$this->toolbar_scroll = $toolBarScroll;      // yes - > Toolbar is always visible on the top of the screen.
		$this->submit_action  = 'submit' . $module->name;
		$this->toolbar_btn    =
			( empty( $toolbarBtn ) || ! is_array( $toolbarBtn ) )
				? array(
				'save' => array(
					'desc' => $module->l( 'Save' ),
					'href' => \AdminController::$currentIndex . '&configure=' . $module->name . '&save' . $module->name .
					          '&token=' . \Tools::getAdminTokenLite( 'AdminModules' ),
				),
				'back' => array(
					'href' => \AdminController::$currentIndex . '&token=' . \Tools::getAdminTokenLite( 'AdminModules' ),
					'desc' => $module->l( 'Back to list' )
				)
			)
				: $toolbarBtn;

		$this->setTab( $startTab, $tabTitle, $tabImage );

		$this->initialized = true;

		return $this;
	}
}