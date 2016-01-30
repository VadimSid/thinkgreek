<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Class BON Form
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Core
 *
 *
*/ 

 
class BON_Form
{
	
	/**
	 * bon_form Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		
	}
	
	/**
	 * Form Declaration
	 *
	 * Creates the opening portion of the form.
	 *
	 * @access	public
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */

	public function form_open($action = '', $attributes = '', $hidden = array()) {

		global $wp;

		if ($attributes == '')
		{
			$attributes = 'method="post"';
		}


		if($action == '') {
			$action = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		}
		// If an action is not a full URL then turn it into one
		else if ($action && strpos($action, '://') === FALSE)
		{
			$action = get_site_url() . '/' . $action;
		}

		$form = '<form action="'.$action.'"';

		$form .= $this->_attributes_to_string($attributes, TRUE);

		$form .= '>';

		if (is_array($hidden) AND count($hidden) > 0)
		{
			$form .= sprintf("<div style=\"display:none\">%s</div>", $this->form_hidden($hidden));
		}

		return $form;
	}

	// ------------------------------------------------------------------------

	/**
	 * Form Declaration - Multipart type
	 *
	 * Creates the opening portion of the form, but with "multipart/form-data".
	 *
	 * @access	public
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	public function form_open_multipart($action = '', $attributes = array(), $hidden = array())
	{
		if (is_string($attributes))
		{
			$attributes .= ' enctype="multipart/form-data"';
		}
		else
		{
			$attributes['enctype'] = 'multipart/form-data';
		}

		return $this->form_open($action, $attributes, $hidden);
	}

	/**
	 * Hidden Input Field
	 *
	 * Generates hidden fields.  You can pass a simple key/value string or an associative
	 * array with multiple values.
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */
	
		public function form_hidden($name, $value = '', $recursing = FALSE)
		{
			static $form;

			if ($recursing === FALSE)
			{
				$form = "\n";
			}

			if (is_array($name))
			{
				foreach ($name as $key => $val)
				{
					$this->form_hidden($key, $val, TRUE);
				}
				return $form;
			}

			if ( ! is_array($value))
			{
				$form .= '<input type="hidden" id="'.$name.'" name="'.$name.'" value="'. $this->form_prep($value, $name).'" />'."\n";
			}
			else
			{
				foreach ($value as $k => $v)
				{
					$k = (is_int($k)) ? '' : $k;
					$this->form_hidden($name.'['.$k.']', $v, TRUE);
				}
			}

			return $form;
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Text Input Field
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_input($data = '', $value = '', $extra = '')
		{
			$defaults = array('type' => 'text', 'id' => (( ! is_array($data)) ? $data : ''), 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

			return "<input ".$this->_parse_form_attributes($data, $defaults).$extra." />";
		}


	// ------------------------------------------------------------------------

	/**
	 * Text Input Field 
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_text($data = '', $value = '', $extra = '')
		{
			return $this->form_input($data, $value, $extra);
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Password Field
	 *
	 * Identical to the input function but adds the "password" type
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_password($data = '', $value = '', $extra = '')
		{
			if ( ! is_array($data))
			{
				$data = array('name' => $data);
			}

			$data['type'] = 'password';
			return $this->form_input($data, $value, $extra);
		}
		
	// ------------------------------------------------------------------------

	/**
	 * Email Field
	 *
	 * Identical to the input function but adds the "email" type
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_email($data = '', $value = '', $extra = '')
		{
			if ( ! is_array($data))
			{
				$data = array('name' => $data);
			}

			$data['type'] = 'email';

			if( function_exists( 'is_email' ) ) {

				if( is_email( $value ) ) {
					$value = $value;
				} else {
					$value = '';
				}

			} else {
				$value = $value;
			}

			return $this->form_input($data, $value, $extra);
		}


	// ------------------------------------------------------------------------

	/**
	 * URL Field
	 *
	 * Identical to the input function but adds the "url" type
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_url($data = '', $value = '', $extra = '')
		{
			if ( ! is_array($data))
			{
				$data = array('name' => $data);
			}

			$data['type'] = 'url';
			return $this->form_input($data, $value, $extra);
		}


	// ------------------------------------------------------------------------

	/**
	 * Number Field
	 *
	 * Identical to the input function but adds the "number" type
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_number($data = '', $value = '', $extra = '')
		{
			if ( ! is_array($data))
			{
				$data = array('name' => $data);
			}

			$data['type'] = 'number';
			return $this->form_input($data, intval( $value ), $extra);
		}
		


	// ------------------------------------------------------------------------

	/**
	 * Textarea field
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_textarea($data = '', $value = '', $extra = '')
		{
			$defaults = array( 'id' => (( ! is_array($data)) ? $data : ''), 'name' => (( ! is_array($data)) ? $data : ''), 'cols' => '40', 'rows' => '10');

			if ( ! is_array($data) OR ! isset($data['value']))
			{
				$val = $value;
			}
			else
			{
				$val = $data['value'];
				unset($data['value']); // textareas don't use the value attribute
			}

			$name = (is_array($data)) ? $data['name'] : $data;
			return "<textarea ".$this->_parse_form_attributes($data, $defaults).$extra.">".$this->form_prep($val, $name)."</textarea>";
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Multi-select menu
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @param	string
	 * @return	type
	 */
	
	public function form_multiselect($name = '', $options = array(), $selected = array(), $extra = '')
	{
		if ( ! strpos($extra, 'multiple'))
		{
			$extra .= ' multiple="multiple"';
		}

		return $this->form_dropdown($name, $options, $selected, $extra);
	}
	

	// --------------------------------------------------------------------

	/**
	 * Drop-down Menu
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_dropdown($name = '', $options = array(), $selected = array(), $extra = '') {

			if ( ! is_array($selected) ) {
				$selected = array($selected);
			}
			/*
			// If no selected state was submitted we will attempt to set it automatically
			if (count($selected) === 0) {
				// If the form name appears in the $_POST array we have a winner!
				if ( isset( $_POST[$name] ) ) {
					$selected = array( $_POST[$name] );
				}
			}*/

			if ($extra != '') $extra = ' '.$extra;


			$defaults = array( 'id' => (( ! is_array($name)) ? $name : ''), 'name' => (( ! is_array($name)) ? $name : '') );

			$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

			$form = "<select ".$this->_parse_form_attributes($name, $defaults).$extra.$multiple. ">\n";

			foreach ($options as $key => $val)
			{
				$key = (string) $key;

				if (is_array($val) && ! empty($val))
				{
					$form .= '<optgroup label="'.$key.'">'."\n";

					foreach ($val as $optgroup_key => $optgroup_val)
					{
						//$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';
						$sel = selected( in_array($optgroup_key, $selected), ture, false );
						$form .= '<option value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
					}

					$form .= '</optgroup>'."\n";
				}
				else
				{
					//$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
					$sel = selected( in_array($key, $selected), true, false );
					$form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
				}
			}

			$form .= '</select>';

			return $form;
		}
	
	// --------------------------------------------------------------------

	/**
	 * Drop-down Menu
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	string
	 * @param	string
	 * @return	string
	 */
		public function form_select($name = '', $options = array(), $selected = array(), $extra = '') {
			return $this->form_dropdown($name, $options, $selected, $extra);
		}

	// ------------------------------------------------------------------------

	/**
	 * Checkbox Field
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	string
	 * @return	string
	 */
	
		public function form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
		{
			$defaults = array('type' => 'checkbox', 'id' => (( ! is_array($data)) ? $data : ''), 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

			if (is_array($data) AND array_key_exists('checked', $data))
			{
				$checked = $data['checked'];

				if ($checked == FALSE)
				{
					unset($data['checked']);
				}
				else
				{
					$data['checked'] = 'checked';
				}
			}

			if ($checked == TRUE)
			{
				$defaults['checked'] = 'checked';
			}
			else
			{
				unset($defaults['checked']);
			}

			return "<input ".$this->_parse_form_attributes($data, $defaults).$extra." />";
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Radio Button
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	string
	 * @return	string
	 */
	
		public function form_radio($data = '', $value = '', $checked = FALSE, $extra = '')
		{
			if ( ! is_array($data))
			{
				$data = array('name' => $data);
			}

			$data['type'] = 'radio';
			return $this->form_checkbox($data, $value, $checked, $extra);
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Submit Button
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_submit($data = '', $value = '', $extra = '')
		{
			$defaults = array('type' => 'submit', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

			return "<input ".$this->_parse_form_attributes($data, $defaults).$extra." />";
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Reset Button
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_reset($data = '', $value = '', $extra = '')
		{
			$defaults = array('type' => 'reset', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

			return "<input ".$this->_parse_form_attributes($data, $defaults).$extra." />";
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Form Button
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	
		public function form_button($data = '', $content = '', $extra = '')
		{
			$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'type' => 'button');

			if ( is_array($data) AND isset($data['content']))
			{
				$content = $data['content'];
				unset($data['content']); // content is not an attribute
			}

			return "<button ".$this->_parse_form_attributes($data, $defaults).$extra.">".$content."</button>";
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Form Label Tag
	 *
	 * @access	public
	 * @param	string	The text to appear onscreen
	 * @param	string	The id the label applies to
	 * @param	string	Additional attributes
	 * @return	string
	 */
	
		public function form_label($label_text = '', $id = '', $attributes = array())
		{

			$label = '<label';

			if ($id != '')
			{
				$label .= " for=\"$id\"";
			}

			if (is_array($attributes) AND count($attributes) > 0)
			{
				foreach ($attributes as $key => $val)
				{
					$label .= ' '.$key.'="'.$val.'"';
				}
			}

			$label .= ">$label_text</label>";

			return $label;
		}
	

	// ------------------------------------------------------------------------
	/**
	 * Decoy Tag
	 *
	 * use form_decoy()
	 *
	 * @access	public
	 */

	
	function form_decoy($data ='')
	{
		if ( ! is_array($data))
			{
				$data = array('name' => $data);
			}

		$data['type'] = 'text';
		return $this->form_input($data, '', 'class="decoy"');

	}

	// ------------------------------------------------------------------------
	/**
	 * Fieldset Tag
	 *
	 * Used to produce <fieldset><legend>text</legend>.  To close fieldset
	 * use form_fieldset_close()
	 *
	 * @access	public
	 * @param	string	The legend text
	 * @param	string	Additional attributes
	 * @return	string
	 */
	
		public function form_fieldset($legend_text = '', $attributes = array())
		{
			$fieldset = "<fieldset";

			$fieldset .= $this->_attributes_to_string($attributes, FALSE);

			$fieldset .= ">\n";

			if ($legend_text != '')
			{
				$fieldset .= "<legend>$legend_text</legend>\n";
			}

			return $fieldset;
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Fieldset Close Tag
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	
		public function form_fieldset_close($extra = '')
		{
			return "</fieldset>".$extra;
		}
	

	// ------------------------------------------------------------------------

	/**
	 * Form Close Tag
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	
		public function form_close($extra = '')
		{
			return "</form>".$extra;
		}
	
	
	// ------------------------------------------------------------------------

	/**
	 * Parse the form attributes
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @access	private
	 * @param	array
	 * @param	array
	 * @return	string
	 */
	private function _parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0)
			{
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val)
		{
			if ($key == 'value')
			{
				$val = $this->form_prep($val, $default['name']);
			}

			$att .= $key . '="' . $val . '" ';
		}

		return $att;
	}

	// ------------------------------------------------------------------------

	/**
	 * Attributes To String
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @access	private
	 * @param	mixed
	 * @param	bool
	 * @return	string
	 */
	private function _attributes_to_string($attributes, $formtag = FALSE)
	{
		if (is_string($attributes) AND strlen($attributes) > 0)
		{
			if ($formtag == TRUE AND strpos($attributes, 'method=') === FALSE)
			{
				$attributes .= ' method="post"';
			}

			if ($formtag == TRUE AND strpos($attributes, 'accept-charset=') === FALSE)
			{
				$attributes .= ' accept-charset="utf-8"';
			}

		return ' '.$attributes;
		}

		if (is_object($attributes) AND count($attributes) > 0)
		{
			$attributes = (array)$attributes;
		}

		if (is_array($attributes) AND count($attributes) > 0)
		{
			$atts = '';

			if ( ! isset($attributes['method']) AND $formtag === TRUE)
			{
				$atts .= ' method="post"';
			}

			if ( ! isset($attributes['accept-charset']) AND $formtag === TRUE)
			{
				$atts .= ' accept-charset="utf-8"';
			}

			foreach ($attributes as $key => $val)
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Form Prep
	 *
	 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function form_prep($str = '', $field_name = '')
	{
		static $prepped_fields = array();

		// if the field name is an array we do this recursively
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->form_prep($val);
			}

			return $str;
		}

		if ($str === '')
		{
			return '';
		}

		// we've already prepped a field with this name
		// @todo need to figure out a way to namespace this so
		// that we know the *exact* field and not just one with
		// the same name
		if (isset($prepped_fields[$field_name]))
		{
			return $str;
		}

		$str = htmlspecialchars($str);

		// In case htmlspecialchars misses these.
		$str = str_replace(array("'", '"'), array("&#39;", "&quot;"), $str);

		if ($field_name != '')
		{
			$prepped_fields[$field_name] = $field_name;
		}

		return $str;
	}


	// ------------------------------------------------------------------------

	/**
	 * Form Value
	 *
	 * Grabs a value from the POST array for the specified field so you can
	 * re-populate an input field or textarea.  If Form Validation
	 * is active it retrieves the info from the validation class
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	
	public function set_value($field = '', $default = '')
	{
		if (FALSE === ($obj = $this->_get_validation_object()))
		{
			if ( ! isset($_REQUEST[$field]))
			{
				return $default;
			}

			return $this->form_prep($_REQUEST[$field], $field);
		}

		return $this->form_prep($obj->set_value($field, $default), $field);
	}
	

	// ------------------------------------------------------------------------

	/**
	 * Set Select
	 *
	 * Let's you set the selected value of a <select> menu via data in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	
	public function set_select($field = '', $value = '', $default = FALSE)
	{
		$obj = $this->_get_validation_object();

		if ($obj === FALSE)
		{
			if ( ! isset($_REQUEST[$field]))
			{
				if (count($_REQUEST) === 0 AND $default == TRUE)
				{
					return ' selected="selected"';
				}
				return '';
			}

			$field = $_REQUEST[$field];

			if (is_array($field))
			{
				if ( ! in_array($value, $field))
				{
					return '';
				}
			}
			else
			{
				if (($field == '' OR $value == '') OR ($field != $value))
				{
					return '';
				}
			}

			return ' selected="selected"';
		}

		return $obj->set_select($field, $value, $default);
	}
	

	// ------------------------------------------------------------------------

	/**
	 * Set Checkbox
	 *
	 * Let's you set the selected value of a checkbox via the value in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	public function set_checkbox($field = '', $value = '', $default = FALSE)
	{
		$obj = $this->_get_validation_object();

		if ($obj === FALSE)
		{
			if ( ! isset($_POST[$field]))
			{
				if (count($_POST) === 0 AND $default == TRUE)
				{
					return ' checked="checked"';
				}
				return '';
			}

			$field = $_POST[$field];

			if (is_array($field))
			{
				if ( ! in_array($value, $field))
				{
					return '';
				}
			}
			else
			{
				if (($field == '' OR $value == '') OR ($field != $value))
				{
					return '';
				}
			}

			return ' checked="checked"';
		}

		return $obj->set_checkbox($field, $value, $default);
	}
	

	// ------------------------------------------------------------------------

	/**
	 * Set Radio
	 *
	 * Let's you set the selected value of a radio field via info in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	public function set_radio($field = '', $value = '', $default = FALSE)
	{
		$obj = $this->_get_validation_object();

		if ($obj === FALSE)
		{
			if ( ! isset($_POST[$field]))
			{
				if (count($_POST) === 0 AND $default == TRUE)
				{
					return ' checked="checked"';
				}
				return '';
			}

			$field = $_POST[$field];

			if (is_array($field))
			{
				if ( ! in_array($value, $field))
				{
					return '';
				}
			}
			else
			{
				if (($field == '' OR $value == '') OR ($field != $value))
				{
					return '';
				}
			}

			return ' checked="checked"';
		}

		return $obj->set_radio($field, $value, $default);
	}
	

	// ------------------------------------------------------------------------

	/**
	 * Form Error
	 *
	 * Returns the error for a specific form field.  This is a helper for the
	 * form validation class.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function form_error($field = '', $prefix = '', $suffix = '')
	{
		if (FALSE === ($obj = $this->_get_validation_object()))
		{
			return '';
		}

		return $obj->error($field, $prefix, $suffix);
	}
	

	// ------------------------------------------------------------------------

	/**
	 * Validation Error String
	 *
	 * Returns all the errors associated with a form submission.  This is a helper
	 * function for the form validation class.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function validation_errors($prefix = '', $suffix = '')
	{
		if (FALSE === ($obj = $this->_get_validation_object()))
		{
			return '';
		}

		return $obj->error_string($prefix, $suffix);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Validation Object
	 *
	 * Determines what the form validation class was instantiated as, fetches
	 * the object and returns it.
	 *
	 * @access	private
	 * @return	mixed
	 */
	private function &_get_validation_object()
	{
		global $bon;

		// We set this as a variable since we're returning by reference.
		$return = FALSE;
		
		if ( FALSE !== ($object = $bon->validation() ) )
		{
			if ( ! isset($object) OR ! is_object($object))
			{
				return $return;
			}
			
			return $object;
		}
		
		return $return;
	}
	


}










