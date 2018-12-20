<?php

/**
 * Class Types_Field_Type_View_Frontend_Abstract
 *
 * @since 2.3
 */
abstract class Types_Field_Type_View_Frontend_Abstract implements Types_Interface_Value {
	/** @var Types_Field_Interface */
	protected $entity;

	/** @var array */
	protected $params;

	/**
	 * @var Types_Interface_Value[]
	 */
	protected $decorators = array();

	protected function add_decorator( Types_Interface_Value $decorator ) {
		$this->decorators[] = $decorator;
	}

	protected function get_decorated_value( $value ) {
		if( $this->is_html_output() ) {
			$this->add_decorator( new Types_View_Decorator_Output_HTML() );
		}

		if( empty( $this->decorators ) ) {
			return $value;
		}

		foreach( $this->decorators as $decorator ) {
			$value = $decorator->get_value( $value, $this->params, $this->entity );
		}

		return $value;
	}

	protected function to_string( $value ) {
		if( empty( $value ) ) {
			return '';
		}

		if( $this->entity->is_repeatable() ) {
			if ( null === toolset_getarr( $this->params, 'index' ) 
				|| '' === toolset_getarr( $this->params, 'index' ) ) {
				$decorator_separator = new Types_View_Decorator_Separator();
				$value = $decorator_separator->get_value( $value, $this->params );
			} else {
				$decorator_index = new Types_View_Decorator_Index();
				$value = $decorator_index->get_value( $value, $this->params );
			}
		}

		return is_array( $value )
			? implode( '', $value )
			: $value;
	}

	protected function filter_field_value_after_decorators( $value_after_decorators, $value_before_decorators = null ) {
		if( ! function_exists( 'apply_filters') ) {
			return $value_after_decorators;
		}

		$value_before_decorators = $value_before_decorators !== null
			? $value_before_decorators
			: $value_after_decorators;

		return apply_filters(
			'types_view',
			$value_after_decorators,
			$value_before_decorators,
			$this->entity->get_type(),
			$this->entity->get_slug(),
			$this->entity->get_title(),
			$this->params
		);
	}

	/**
	 * Checks if user wants raw output
	 *
	 * @return bool
	 */
	protected function is_raw_output() {
		// first check "output" param
		if( isset( $this->params['output'] ) && $this->params['output'] == 'raw' ) {
			return true;
		}

		// check legacy "raw" param
		// (the "&& $this->params['raw']" part is important, because user write things like '... raw="false" ...')
		if( isset( $this->params['raw'] ) && $this->params['raw'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if user wants html output
	 * You may think that there is only 'raw' & 'html' but that's not the case. 'html' adds an extra div surrounding
	 * with a bunch of css class build out of the name and type of the field. 'raw' just outputs the value, e.g. for
	 * an image it would be just the url. Whereas neither 'raw' or 'html' would output the image tag and 'html' would
	 * add another div surrounding the image with extra classes.
	 *
	 * @return bool
	 */
	protected function is_html_output() {
		if( $this->is_raw_output() ) {
			// 'raw' and 'html' do not work together... raw has higher priority
			return false;
		}

		if( isset( $this->params['output'] ) && $this->params['output'] == 'html' ) {
			return true;
		}

		return false;
	}
}