<?php

/**
 * This add extra css class to the output
 * Used for types shortcode using attribute "output='html'"
 *
 * Class Types_View_Decorator_Output_HTML
 */
class Types_View_Decorator_Output_HTML implements Types_Interface_Value {

	/**
	 * @param string $value
	 * @param array $params
	 *
	 * @param null|Types_Field_Interface $field
	 *
	 * @return string
	 */
	public function get_value( $value = '', $params = array(), $field = null ) {
		if( ! $field instanceof Types_Field_Interface ) {
			return $value;
		}

		if( ! preg_match( '#(\<[a-z])(.*)(\>)#', $value ) ) {
			// the value does not contain any html
			// (e.g. this wouldn't make sense for fields like 'checkboxes')
			$class_field_value = 'wpcf-field-value ' .
			                     'wpcf-field-' . $field->get_type() . '-value ' .
			                     'wpcf-field-' . $field->get_slug() . '-value';

			$value = '<span class="' . $class_field_value . '">' . $value . '</span>';
		}

		return '<div id="wpcf-field-' . $field->get_slug() .
		       '" class="wpcf-field-' . $field->get_type() . ' wpcf-field-' . $field->get_slug() . '">' .
		       $value .
		       '</div>';
	}
}