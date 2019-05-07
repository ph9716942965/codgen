<?php
namespace HTML;

class Form {
	
	public $action		= array();
	public $heading		= array();
	public $auto_heading= TRUE;
	public $caption		= NULL;
	public $template	= NULL;
	public $newline		= "\n";
	public $empty_cells	= '';
	public $function	= NULL;
	
	public $site_url='';
    
    public static function test(){

        return "mytestClass";
	}
	
	public function __construct($config = array()){
		
	}

	public function form_open($action = '', $attributes = array(), $hidden = array()){
		//$form=new Form();
		if ( ! $action){
			$action = $this->site_url;
		}
		elseif (strpos($action, '://') === FALSE){
			$action = $this->site_url;
		}

		$attributes = $this->_attributes_to_string($attributes);
		if (stripos($attributes, 'method=') === FALSE){
			$attributes .= ' method="post"';
		}
		if (stripos($attributes, 'accept-charset=') === FALSE){
			$attributes .= ' accept-charset="'.strtolower('charset').'"';
		}

		$form = '<form action="'.$action.'"'.$attributes.">\n";

		if (is_array($hidden)){
			foreach ($hidden as $name => $value){
				$form .= '<input type="hidden" name="'.$name.'" value="'.$this->html_escape($value).'" />'."\n";
			}
		}

		return $form;
	}


	function form_open_multipart($action = '', $attributes = array(), $hidden = array()){
		if (is_string($attributes)){
			$attributes .= ' enctype="multipart/form-data"';
		} else {
			$attributes['enctype'] = 'multipart/form-data';
		}
		return $this->form_open($action, $attributes, $hidden);
	}


private	function html_escape($var, $double_encode = TRUE){
		if (empty($var)){
			return $var;
		}

		if (is_array($var)){
			foreach (array_keys($var) as $key){
				$var[$key] = $this->html_escape($var[$key], $double_encode);
			}
			return $var;
		}
		return htmlspecialchars($var);
		//return htmlspecialchars($var, ENT_QUOTES, config_item('charset'), $double_encode);
	}

public function form_hidden($name, $value = '', $recursing = FALSE){
		static $form;
		if ($recursing === FALSE){
			$form = "\n";
		}
		if (is_array($name)){
			foreach ($name as $key => $val){
				$this->form_hidden($key, $val, TRUE);
			}
			return $form;
		}
		if ( ! is_array($value)){
			$form .= '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($value)."\" />\n";
		} else {
			foreach ($value as $k => $v){
				$k = is_int($k) ? '' : $k;
				form_hidden($name.'['.$k.']', $v, TRUE);
			}
		}
		return $form;
	}


public function form_input($data = '', $value = '', $extra = ''){
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);
		return '<input '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra)." />\n";
	}
	

private function _parse_form_attributes($attributes, $default){
		if (is_array($attributes)){
			foreach ($default as $key => $val){
				if (isset($attributes[$key])){
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}
			if (count($attributes) > 0){
				$default = array_merge($default, $attributes);
			}
		}
		$att = '';
		foreach ($default as $key => $val){
			if ($key === 'value'){
				$val = $this->html_escape($val);
			}elseif ($key === 'name' && ! strlen($default['name'])){
				continue;
			}
			$att .= $key.'="'.$val.'" ';
		}
		return $att;
	}


private	function _attributes_to_string($attributes){
		if (empty($attributes)){
			return '';
		}
		if (is_object($attributes)){
			$attributes = (array) $attributes;
		}
		if (is_array($attributes)){
			$atts = '';
			foreach ($attributes as $key => $val){
				$atts .= ' '.$key.'="'.$val.'"';
			}
			return $atts;
		}
		if (is_string($attributes)){
			return ' '.$attributes;
		}
		return FALSE;
	}

public function form_password($data = '', $value = '', $extra = ''){
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'password';
		return $this->form_input($data, $value, $extra);
	}

public function form_upload($data = '', $value = '', $extra = ''){
		$defaults = array('type' => 'file', 'name' => '');
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'file';
		return '<input '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra)." />\n";
	}

public function form_textarea($data = '', $value = '', $extra = ''){
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'cols' => '40',
			'rows' => '10'
		);
		if ( ! is_array($data) OR ! isset($data['value'])){
			$val = $value;
		} else {
			$val = $data['value'];
			unset($data['value']); // textareas don't use the value attribute
		}
		return '<textarea '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra).'>'
			.$this->html_escape($val)
			."</textarea>\n";
	}

public function form_multiselect($name = '', $options = array(), $selected = array(), $extra = ''){
		$extra = $this->_attributes_to_string($extra);
		if (stripos($extra, 'multiple') === FALSE){
			$extra .= ' multiple="multiple"';
		}
		return $this->form_dropdown($name, $options, $selected, $extra);
	}


public function form_dropdown($data = '', $options = array(), $selected = array(), $extra = ''){
		$defaults = array();
		if (is_array($data)){
			if (isset($data['selected'])){
				$selected = $data['selected'];
				unset($data['selected']); // select tags don't have a selected attribute
			}

			if (isset($data['options'])){
				$options = $data['options'];
				unset($data['options']); // select tags don't use an options attribute
			}
		}	else {
			$defaults = array('name' => $data);
		}

		is_array($selected) OR $selected = array($selected);
		is_array($options) OR $options = array($options);
		// If no selected state was submitted we will attempt to set it automatically
		if (empty($selected)){
			if (is_array($data)){
				if (isset($data['name'], $_POST[$data['name']])){
					$selected = array($_POST[$data['name']]);
				}
			}elseif (isset($_POST[$data])){
				$selected = array($_POST[$data]);
			}
		}

		$extra = $this->_attributes_to_string($extra);
		$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';
		$form = '<select '.rtrim($this->_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";
		foreach ($options as $key => $val){
			$key = (string) $key;
			if (is_array($val)){
				if (empty($val)){
					continue;
				}
				$form .= '<optgroup label="'.$key."\">\n";
				foreach ($val as $optgroup_key => $optgroup_val){
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.$this->html_escape($optgroup_key).'"'.$sel.'>'
						.(string) $optgroup_val."</option>\n";
				}
				$form .= "</optgroup>\n";
			}else{
				$form .= '<option value="'.$this->html_escape($key).'"'
					.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $val."</option>\n";
			}
		}
		return $form."</select>\n";
	}



public function form_checkbox($data = '', $value = '', $checked = FALSE, $extra = ''){
		$defaults = array('type' => 'checkbox', 'name' => ( ! is_array($data) ? $data : ''), 'value' => $value);
		if (is_array($data) && array_key_exists('checked', $data)){
			$checked = $data['checked'];
			if ($checked == FALSE){
				unset($data['checked']);
			}else{
				$data['checked'] = 'checked';
			}
		}
		if ($checked == TRUE){
			$defaults['checked'] = 'checked';
		}else{
			unset($defaults['checked']);
		}
		return '<input '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra)." />\n";
	}

public	function form_radio($data = '', $value = '', $checked = FALSE, $extra = ''){
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'radio';
		return $this->form_checkbox($data, $value, $checked, $extra);
	}





	function form_submit($data = '', $value = '', $extra = ''){
		$defaults = array(
			'type' => 'submit',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);
		return '<input '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra)." />\n";
	}


	function form_reset($data = '', $value = '', $extra = ''){
		$defaults = array(
			'type' => 'reset',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);
	return '<input '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra)." />\n";
	}




	function form_button($data = '', $content = '', $extra = ''){
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'type' => 'button'
		);
		if (is_array($data) && isset($data['content'])){
			$content = $data['content'];
			unset($data['content']); // content is not an attribute
		}

		return '<button '.$this->_parse_form_attributes($data, $defaults).$this->_attributes_to_string($extra).'>'
			.$content
			."</button>\n";
	}


public function form_label($label_text = '', $id = '', $attributes = array()){
		$label = '<label';
		if ($id !== ''){
			$label .= ' for="'.$id.'"';
		}
		$label .= $this->_attributes_to_string($attributes);
		return $label.'>'.$label_text.'</label>';
	}


public	function form_fieldset($legend_text = '', $attributes = array()){
		$fieldset = '<fieldset'.$this->_attributes_to_string($attributes).">\n";
		if ($legend_text !== ''){
			return $fieldset.'<legend>'.$legend_text."</legend>\n";
		}
		return $fieldset;
	}

	function form_fieldset_close($extra = ''){
		return '</fieldset>'.$extra;
	}


	function form_close($extra = ''){
		return '</form>'.$extra;
	}


	function form_prep($str){
		return $this->html_escape($str, TRUE);
	}

	function &get_instance()
	{
		return Form::get_instance();
	}

	function set_value($field, $default = '', $html_escape = TRUE){
		//$CI =$this->get_instance();
		// $value = (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
		// 	? $CI->form_validation->set_value($field, $default)
		// 	: $CI->input->post($field, FALSE);
		 isset($value) OR $value = $default;
		return ($html_escape) ? $this->html_escape($value) : $value;
	}


	function set_select($field, $value = '', $default = FALSE){
		//$CI =& get_instance();
		// if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field)){
		// 	return $CI->form_validation->set_select($field, $value, $default);
		// }elseif (($input = $CI->input->post($field, FALSE)) === NULL){
		// 	return ($default === TRUE) ? ' selected="selected"' : '';
		// }
		$value = (string) $value;
		if (is_array($input)){
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v){
				if ($value === $v){
					return ' selected="selected"';
				}
			}
			return '';
		}
		return ($input === $value) ? ' selected="selected"' : '';
	}


	function set_checkbox($field, $value = '', $default = FALSE){
		// $CI =& get_instance();
		// if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field)){
		// 	return $CI->form_validation->set_checkbox($field, $value, $default);
		// }
		// Form inputs are always strings ...
		$value = (string) $value;
		$input = $_REQUEST[$field];
		if (is_array($input)){
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v){
				if ($value === $v){
					return ' checked="checked"';
				}
			}
			return '';
		}

		// Unchecked checkbox and radio inputs are not even submitted by browsers ...
		if ($_SERVER['REQUEST_METHOD'] === 'post'){
			return ($input === $value) ? ' checked="checked"' : '';
		}
		return ($default === TRUE) ? ' checked="checked"' : '';
	}


	function set_radio($field, $value = '', $default = FALSE){
		// $CI =& get_instance();
		// if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field)){
		// 	return $CI->form_validation->set_radio($field, $value, $default);
		// }
		// Form inputs are always strings ...
		$value = (string) $value;
		$input = $_REQUEST[$field];
		if (is_array($input)){
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v){
				if ($value === $v){
					return ' checked="checked"';
				}
			}
			return '';
		}

		// Unchecked checkbox and radio inputs are not even submitted by browsers ...
		if ($_REQUEST){
			return ($input === $value) ? ' checked="checked"' : '';
		}
		return ($default === TRUE) ? ' checked="checked"' : '';
	}



	function form_error($field = '', $prefix = '', $suffix = ''){
		if (FALSE === ($OBJ =& _get_validation_object())){
			return '';
		}
		return $OBJ->error($field, $prefix, $suffix);
	}


	function validation_errors($prefix = '', $suffix = ''){
		if (FALSE === ($OBJ =& _get_validation_object())){
			return '';
		}
		return $OBJ->error_string($prefix, $suffix);
	}


	function &_get_validation_object(){
		//$CI =& get_instance();
		// We set this as a variable since we're returning by reference.
		$return = FALSE;
		if (FALSE !== ($object = $CI->load->is_loaded('Form_validation'))){
			if ( ! isset($CI->$object) OR ! is_object($CI->$object)){
				return $return;
			}
			return $CI->$object;
		}
		return $return;
	}

}
