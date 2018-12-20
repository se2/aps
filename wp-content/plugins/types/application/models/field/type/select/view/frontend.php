<?php

/**
 * Class Types_Field_Type_Select_View_Frontend
 *
 * Handles view specific tasks for field "Select"
 *
 * @since 2.3
 */
class Types_Field_Type_Select_View_Frontend extends Types_Field_Type_View_Frontend_Abstract {

	/**
	 * Types_Field_Type_Select_View_Frontend constructor.
	 *
	 * @param Types_Field_Type_Select $entity
	 * @param array $params
	 */
	public function __construct( Types_Field_Type_Select $entity, $params = array() ) {
		$this->entity = $entity;
		$this->params = $params;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		$options = $this->entity->get_options();

		if( empty( $options ) ) {
			return '';
		}

		foreach( $options as $option ) {
			if( $option->is_active() ) {
				$active_option = $option;
				break;
			}
		}

		if( ! isset( $active_option ) ) {
			return '';
		}

		$filtered = $active_option->get_value_filtered( $this->params );
		if( $filtered !== $active_option->get_value_raw() && $filtered !== $active_option->get_value() ) {
			// filter has highest priority
			return $this->filter_field_value_after_decorators( $filtered );
		}

		if ( $this->is_raw_output() ) {
			return $this->filter_field_value_after_decorators( $active_option->get_value_raw() );
		}

		if( isset( $this->params['option'] ) ) {
			return $this->filter_field_value_after_decorators( $this->get_user_value( $active_option ) );
		}

		return $this->filter_field_value_after_decorators( $active_option->get_value() );
	}

	/**
	 * @param Types_Field_Part_Option $active_option
	 *
	 * @return mixed|string
	 */
	private function get_user_value( Types_Field_Part_Option $active_option ){
		if( $active_option->get_id() != $this->params['option'] ) {
			return '';
		}

		return isset( $this->params['content'] )
			? $this->params['content']
			: $active_option->get_value();
	}
}
