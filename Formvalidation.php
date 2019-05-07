<?php
namespace HTML;
require_once('C:\xampp\htdocs\ecomm.local\cg\Form.php');

class Formvalidation extends Form{

	protected $CI;
	protected $_field_data		= array();
	protected $_config_rules	= array();
	protected $_error_array		= array();
	protected $_error_messages	= array();
	protected $_error_prefix	= '<p>';
	protected $_error_suffix	= '</p>';
	protected $error_string		= '';
	protected $_safe_form_data	= FALSE;
	public $validation_data	= array();

	public function __construct($rules = array())
	{
        parent::__construct();
		//$this->CI =& get_instance();

		// applies delimiters set in config file.
		if (isset($rules['error_prefix'])){
			$this->_error_prefix = $rules['error_prefix'];
			unset($rules['error_prefix']);
		}
		if (isset($rules['error_suffix'])){
			$this->_error_suffix = $rules['error_suffix'];
			unset($rules['error_suffix']);
		}

		$this->_config_rules = $rules;
		//$this->CI->load->helper('form');
		$this->log_message('info', 'Form Validation Class Initialized');
	}

    function log_message($level, $message){
		static $_log;
		if ($_log === NULL){
			//$_log[0] =& load_class('Log', 'core');
		}
		//$_log[0]->write_log($level, $message);
    }
    
	public function set_rules($field, $label = '', $rules = array(), $errors = array()){
		if ($_POST && empty($this->validation_data)){
			return $this;
		}

		if (is_array($field)){
			foreach ($field as $row){
				
				if ( ! isset($row['field'], $row['rules'])){
					continue;
				}

				$label = isset($row['label']) ? $row['label'] : $row['field'];
				$errors = (isset($row['errors']) && is_array($row['errors'])) ? $row['errors'] : array();
				$this->set_rules($row['field'], $label, $row['rules'], $errors);
			}
			return $this;
		}

		if ( ! is_string($field) OR $field === '' OR empty($rules)){
			return $this;
		}
		elseif ( ! is_array($rules)){
			if ( ! is_string($rules)){
				return $this;
			}
			$rules = preg_split('/\|(?![^\[]*\])/', $rules);
		}

		$label = ($label === '') ? $field : $label;
		$indexes = array();
		if (($is_array = (bool) preg_match_all('/\[(.*?)\]/', $field, $matches)) === TRUE){
			sscanf($field, '%[^[][', $indexes[0]);
			for ($i = 0, $c = count($matches[0]); $i < $c; $i++){
				if ($matches[1][$i] !== ''){
					$indexes[] = $matches[1][$i];
				}
			}
		}

		// Build our master array
		$this->_field_data[$field] = array(
			'field'		=> $field,
			'label'		=> $label,
			'rules'		=> $rules,
			'errors'	=> $errors,
			'is_array'	=> $is_array,
			'keys'		=> $indexes,
			'postdata'	=> NULL,
			'error'		=> ''
		);

		return $this;
	}

	
	public function set_data(array $data){
		if ( ! empty($data)){
			$this->validation_data = $data;
		}
		return $this;
	}

	public function set_message($lang, $val = ''){
		if ( ! is_array($lang)){
			$lang = array($lang => $val);
		}

		$this->_error_messages = array_merge($this->_error_messages, $lang);
		return $this;
	}

	
	public function set_error_delimiters($prefix = '<p>', $suffix = '</p>'){
		$this->_error_prefix = $prefix;
		$this->_error_suffix = $suffix;
		return $this;
	}

	
	public function error($field, $prefix = '', $suffix = ''){
		if (empty($this->_field_data[$field]['error'])){
			return '';
		}
		if ($prefix === ''){
			$prefix = $this->_error_prefix;
		}
		if ($suffix === ''){
			$suffix = $this->_error_suffix;
		}
		return $prefix.$this->_field_data[$field]['error'].$suffix;
	}

	public function error_array(){
		return $this->_error_array;
	}

	public function error_string($prefix = '', $suffix = ''){
		// No errors, validation passes!
		if (count($this->_error_array) === 0){
			return '';
		}

		if ($prefix === ''){
			$prefix = $this->_error_prefix;
		}

		if ($suffix === ''){
			$suffix = $this->_error_suffix;
		}

		// Generate the error string
		$str = '';
		foreach ($this->_error_array as $val){
			if ($val !== ''){
				$str .= $prefix.$val.$suffix."\n";
			}
		}

		return $str;
	}

	public function run($group = ''){
		$validation_array = empty($this->validation_data)
			? $_POST
			: $this->validation_data;
       
		if (count($this->_field_data) === 0){
            echo "174\n";
			// No validation rules?  We're done...
			if (count($this->_config_rules) === 0){
                echo "177\n";
               // print_r($this->_field_data);
				return FALSE;
			}

			if (empty($group)){
				// Is there a validation rule for the particular URI being accessed?
				$group = trim($this->CI->uri->ruri_string(), '/');
				isset($this->_config_rules[$group]) OR $group = $this->CI->router->class.'/'.$this->CI->router->method;
			}

			$this->set_rules(isset($this->_config_rules[$group]) ? $this->_config_rules[$group] : $this->_config_rules);

			if (count($this->_field_data) === 0){
				log_message('debug', 'Unable to find validation rules');
				return FALSE;
			}
		}
        echo '195';print_r($validation_array);exit;
		// Load the language file containing error messages
		$this->CI->lang->load('form_validation');

		// Cycle through the rules for each field and match the corresponding $validation_data item
		foreach ($this->_field_data as $field => &$row){
			if ($row['is_array'] === TRUE){
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($validation_array, $row['keys']);
			}
			elseif (isset($validation_array[$field])){
				$this->_field_data[$field]['postdata'] = $validation_array[$field];
			}
		}

		foreach ($this->_field_data as $field => &$row){
			// Don't try to validate if we have no rules set
			if (empty($row['rules'])){
				continue;
			}

			$this->_execute($row, $row['rules'], $row['postdata']);
		}

		// Did we end up with any errors?
		$total_errors = count($this->_error_array);
		if ($total_errors > 0){
			$this->_safe_form_data = TRUE;
		}

		// Now we need to re-set the POST data with the new, processed data
		empty($this->validation_data) && $this->_reset_post_array();
		return ($total_errors === 0);
    }
    
	protected function _prepare_rules($rules){
		$new_rules = array();
		$callbacks = array();

		foreach ($rules as &$rule){
			// Let 'required' always be the first (non-callback) rule
			if ($rule === 'required'){
				array_unshift($new_rules, 'required');
			}
			// 'isset' is a kind of a weird alias for 'required' ...
			elseif ($rule === 'isset' && (empty($new_rules) OR $new_rules[0] !== 'required')){
				array_unshift($new_rules, 'isset');
			}
			// The old/classic 'callback_'-prefixed rules
			elseif (is_string($rule) && strncmp('callback_', $rule, 9) === 0){
				$callbacks[] = $rule;
			}
			// Proper callables
			elseif (is_callable($rule)){
				$callbacks[] = $rule;
			}
			// "Named" callables; i.e. array('name' => $callable)
			elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1])){
				$callbacks[] = $rule;
			}
			// Everything else goes at the end of the queue
			else{
				$new_rules[] = $rule;
			}
		}

		return array_merge($callbacks, $new_rules);
	}

	protected function _reduce_array($array, $keys, $i = 0){
		if (is_array($array) && isset($keys[$i])){
			return isset($array[$keys[$i]]) ? $this->_reduce_array($array[$keys[$i]], $keys, ($i+1)) : NULL;
		}

		// NULL must be returned for empty fields
		return ($array === '') ? NULL : $array;
	}

	protected function _reset_post_array(){
		foreach ($this->_field_data as $field => $row){
			if ($row['postdata'] !== NULL){
				if ($row['is_array'] === FALSE){
					isset($_POST[$field]) && $_POST[$field] = is_array($row['postdata']) ? NULL : $row['postdata'];
				} else {
					// start with a reference
					$post_ref =& $_POST;

					// before we assign values, make a reference to the right POST key
					if (count($row['keys']) === 1){
						$post_ref =& $post_ref[current($row['keys'])];
					} else {
						foreach ($row['keys'] as $val){
							$post_ref =& $post_ref[$val];
						}
					}

					$post_ref = $row['postdata'];
				}
			}
		}
	}

	
	protected function _execute($row, $rules, $postdata = NULL, $cycles = 0){
		
		if (is_array($postdata) && ! empty($postdata)){
			foreach ($postdata as $key => $val){
				$this->_execute($row, $rules, $val, $key);
			}

			return;
		}

		$rules = $this->_prepare_rules($rules);
		foreach ($rules as $rule){
			$_in_array = FALSE;

			// We set the $postdata variable with the current data in our master array so that
			// each cycle of the loop is dealing with the processed data from the last cycle
			if ($row['is_array'] === TRUE && is_array($this->_field_data[$row['field']]['postdata'])){
				// We shouldn't need this safety, but just in case there isn't an array index
				// associated with this cycle we'll bail out
				if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles])){
					continue;
				}

				$postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
				$_in_array = TRUE;
			}else{

				$postdata = is_array($this->_field_data[$row['field']]['postdata'])
					? NULL
					: $this->_field_data[$row['field']]['postdata'];
			}

			// Is the rule a callback?
			$callback = $callable = FALSE;
			if (is_string($rule))
			{
				if (strpos($rule, 'callback_') === 0)
				{
					$rule = substr($rule, 9);
					$callback = TRUE;
				}
			}
			elseif (is_callable($rule))
			{
				$callable = TRUE;
			}
			elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1]))
			{
				// We have a "named" callable, so save the name
				$callable = $rule[0];
				$rule = $rule[1];
			}

			// Strip the parameter (if exists) from the rule
			// Rules can contain a parameter: max_length[5]
			$param = FALSE;
			if ( ! $callable && preg_match('/(.*?)\[(.*)\]/', $rule, $match))
			{
				$rule = $match[1];
				$param = $match[2];
			}

			// Ignore empty, non-required inputs with a few exceptions ...
			if (
				($postdata === NULL OR $postdata === '')
				&& $callback === FALSE
				&& $callable === FALSE
				&& ! in_array($rule, array('required', 'isset', 'matches'), TRUE)
			)
			{
				continue;
			}

			// Call the function that corresponds to the rule
			if ($callback OR $callable !== FALSE)
			{
				if ($callback)
				{
					if ( ! method_exists($this->CI, $rule))
					{
						log_message('debug', 'Unable to find callback validation rule: '.$rule);
						$result = FALSE;
					}
					else
					{
						// Run the function and grab the result
						$result = $this->CI->$rule($postdata, $param);
					}
				}
				else
				{
					$result = is_array($rule)
						? $rule[0]->{$rule[1]}($postdata)
						: $rule($postdata);

					// Is $callable set to a rule name?
					if ($callable !== FALSE)
					{
						$rule = $callable;
					}
				}

				// Re-assign the result to the master data array
				if ($_in_array === TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
				}
			}
			elseif ( ! method_exists($this, $rule))
			{
				// If our own wrapper function doesn't exist we see if a native PHP function does.
				// Users can use any native PHP function call that has one param.
				if (function_exists($rule))
				{
					// Native PHP functions issue warnings if you pass them more parameters than they use
					$result = ($param !== FALSE) ? $rule($postdata, $param) : $rule($postdata);

					if ($_in_array === TRUE)
					{
						$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
					}
					else
					{
						$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
					}
				}
				else
				{
					log_message('debug', 'Unable to find validation rule: '.$rule);
					$result = FALSE;
				}
			}
			else
			{
				$result = $this->$rule($postdata, $param);

				if ($_in_array === TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
				}
			}

			// Did the rule test negatively? If so, grab the error.
			if ($result === FALSE)
			{
				// Callable rules might not have named error messages
				if ( ! is_string($rule))
				{
					$line = $this->CI->lang->line('form_validation_error_message_not_set').'(Anonymous function)';
				}
				else
				{
					$line = $this->_get_error_message($rule, $row['field']);
				}

				// Is the parameter we are inserting into the error message the name
				// of another field? If so we need to grab its "field label"
				if (isset($this->_field_data[$param], $this->_field_data[$param]['label']))
				{
					$param = $this->_translate_fieldname($this->_field_data[$param]['label']);
				}

				// Build the error message
				$message = $this->_build_error_msg($line, $this->_translate_fieldname($row['label']), $param);

				// Save the error message
				$this->_field_data[$row['field']]['error'] = $message;

				if ( ! isset($this->_error_array[$row['field']]))
				{
					$this->_error_array[$row['field']] = $message;
				}

				return;
			}
		}
	}


	protected function _get_error_message($rule, $field)
	{
		// check if a custom message is defined through validation config row.
		if (isset($this->_field_data[$field]['errors'][$rule]))
		{
			return $this->_field_data[$field]['errors'][$rule];
		}
		// check if a custom message has been set using the set_message() function
		elseif (isset($this->_error_messages[$rule]))
		{
			return $this->_error_messages[$rule];
		}
		elseif (FALSE !== ($line = $this->CI->lang->line('form_validation_'.$rule)))
		{
			return $line;
		}
		// DEPRECATED support for non-prefixed keys, lang file again
		elseif (FALSE !== ($line = $this->CI->lang->line($rule, FALSE)))
		{
			return $line;
		}

		return $this->CI->lang->line('form_validation_error_message_not_set').'('.$rule.')';
	}


	protected function _translate_fieldname($fieldname)
	{
		// Do we need to translate the field name? We look for the prefix 'lang:' to determine this
		// If we find one, but there's no translation for the string - just return it
		if (sscanf($fieldname, 'lang:%s', $line) === 1 && FALSE === ($fieldname = $this->CI->lang->line($line, FALSE)))
		{
			return $line;
		}

		return $fieldname;
	}

	
	protected function _build_error_msg($line, $field = '', $param = '')
	{
		// Check for %s in the string for legacy support.
		if (strpos($line, '%s') !== FALSE)
		{
			return sprintf($line, $field, $param);
		}

		return str_replace(array('{field}', '{param}'), array($field, $param), $line);
	}

	
	public function has_rule($field)
	{
		return isset($this->_field_data[$field]);
	}

	
	public function set_value($field = '', $default = '')
	{
		if ( ! isset($this->_field_data[$field], $this->_field_data[$field]['postdata']))
		{
			return $default;
		}

		// If the data is an array output them one at a time.
		//	E.g: form_input('name[]', set_value('name[]');
		if (is_array($this->_field_data[$field]['postdata']))
		{
			return array_shift($this->_field_data[$field]['postdata']);
		}

		return $this->_field_data[$field]['postdata'];
	}

	
	public function set_select($field = '', $value = '', $default = FALSE)
	{
		if ( ! isset($this->_field_data[$field], $this->_field_data[$field]['postdata']))
		{
			return ($default === TRUE && count($this->_field_data) === 0) ? ' selected="selected"' : '';
		}

		$field = $this->_field_data[$field]['postdata'];
		$value = (string) $value;
		if (is_array($field))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($field as &$v)
			{
				if ($value === $v)
				{
					return ' selected="selected"';
				}
			}

			return '';
		}
		elseif (($field === '' OR $value === '') OR ($field !== $value))
		{
			return '';
		}

		return ' selected="selected"';
	}

	
	
	public function set_radio($field = '', $value = '', $default = FALSE)
	{
		if ( ! isset($this->_field_data[$field], $this->_field_data[$field]['postdata']))
		{
			return ($default === TRUE && count($this->_field_data) === 0) ? ' checked="checked"' : '';
		}

		$field = $this->_field_data[$field]['postdata'];
		$value = (string) $value;
		if (is_array($field))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($field as &$v)
			{
				if ($value === $v)
				{
					return ' checked="checked"';
				}
			}

			return '';
		}
		elseif (($field === '' OR $value === '') OR ($field !== $value))
		{
			return '';
		}

		return ' checked="checked"';
	}


	public function set_checkbox($field = '', $value = '', $default = FALSE)
	{
		// Logic is exactly the same as for radio fields
		return $this->set_radio($field, $value, $default);
	}

	
	public function required($str)
	{
		return is_array($str)
			? (empty($str) === FALSE)
			: (trim($str) !== '');
	}

	
	public function regex_match($str, $regex)
	{
		return (bool) preg_match($regex, $str);
	}

	
	public function matches($str, $field)
	{
		return isset($this->_field_data[$field], $this->_field_data[$field]['postdata'])
			? ($str === $this->_field_data[$field]['postdata'])
			: FALSE;
	}


	public function differs($str, $field)
	{
		return ! (isset($this->_field_data[$field]) && $this->_field_data[$field]['postdata'] === $str);
	}


	public function is_unique($str, $field)
	{
		sscanf($field, '%[^.].%[^.]', $table, $field);
		return isset($this->CI->db)
			? ($this->CI->db->limit(1)->get_where($table, array($field => $str))->num_rows() === 0)
			: FALSE;
	}

	

	public function min_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return ($val <= mb_strlen($str));
	}


	public function max_length($str, $val)
	{
        print_r($GLOBALS);exit;
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return ($val >= mb_strlen($str));
	}


	public function exact_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return (mb_strlen($str) === (int) $val);
	}

	public function valid_url($str)
	{
		if (empty($str))
		{
			return FALSE;
		}
		elseif (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', $str, $matches))
		{
			if (empty($matches[2]))
			{
				return FALSE;
			}
			elseif ( ! in_array(strtolower($matches[1]), array('http', 'https'), TRUE))
			{
				return FALSE;
			}

			$str = $matches[2];
		}

	
		if (preg_match('/^\[([^\]]+)\]/', $str, $matches) && ! is_php('7') && filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE)
		{
			$str = 'ipv6.host'.substr($str, strlen($matches[1]) + 2);
		}

		return (filter_var('http://'.$str, FILTER_VALIDATE_URL) !== FALSE);
	}


	public function valid_email($str)
	{
		if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches))
		{
			$domain = defined('INTL_IDNA_VARIANT_UTS46')
				? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46)
				: idn_to_ascii($matches[2]);

			if ($domain !== FALSE)
			{
				$str = $matches[1].'@'.$domain;
			}
		}

		return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
	}


	public function valid_emails($str)
	{
		if (strpos($str, ',') === FALSE)
		{
			return $this->valid_email(trim($str));
		}

		foreach (explode(',', $str) as $email)
		{
			if (trim($email) !== '' && $this->valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}


	public function valid_ip($ip, $which = '')
	{
		return $this->CI->input->valid_ip($ip, $which);
	}


	public function alpha($str)
	{
		return ctype_alpha($str);
	}


	public function alpha_numeric($str)
	{
		return ctype_alnum((string) $str);
	}


	public function alpha_numeric_spaces($str)
	{
		return (bool) preg_match('/^[A-Z0-9 ]+$/i', $str);
	}


	public function alpha_dash($str)
	{
		return (bool) preg_match('/^[a-z0-9_-]+$/i', $str);
	}


	public function numeric($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}


	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	
	public function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}

	
	public function greater_than($str, $min)
	{
		return is_numeric($str) ? ($str > $min) : FALSE;
	}

	public function greater_than_equal_to($str, $min)
	{
		return is_numeric($str) ? ($str >= $min) : FALSE;
	}


	public function less_than($str, $max)
	{
		return is_numeric($str) ? ($str < $max) : FALSE;
	}


	public function less_than_equal_to($str, $max)
	{
		return is_numeric($str) ? ($str <= $max) : FALSE;
	}


	public function in_list($value, $list)
	{
		return in_array($value, explode(',', $list), TRUE);
	}


	public function is_natural($str)
	{
		return ctype_digit((string) $str);
	}


	public function is_natural_no_zero($str)
	{
		return ($str != 0 && ctype_digit((string) $str));
	}

	// --------------------------------------------------------------------

	
	public function valid_base64($str)
	{
		return (base64_encode(base64_decode($str)) === $str);
	}

	
	public function prep_for_form($data)
	{
		if ($this->_safe_form_data === FALSE OR empty($data))
		{
			return $data;
		}

		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				$data[$key] = $this->prep_for_form($val);
			}

			return $data;
		}

		return str_replace(array("'", '"', '<', '>'), array('&#39;', '&quot;', '&lt;', '&gt;'), stripslashes($data));
	}

	
	public function prep_url($str = '')
	{
		if ($str === 'http://' OR $str === '')
		{
			return '';
		}

		if (strpos($str, 'http://') !== 0 && strpos($str, 'https://') !== 0)
		{
			return 'http://'.$str;
		}

		return $str;
	}

	
	public function strip_image_tags($str)
	{
		return $this->CI->security->strip_image_tags($str);
	}

	
	public function encode_php_tags($str)
	{
		return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
	}

	
	public function reset_validation()
	{
		$this->_field_data = array();
		$this->_error_array = array();
		$this->_error_messages = array();
		$this->error_string = '';
		return $this;
	}

}
