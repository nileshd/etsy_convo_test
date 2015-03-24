<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Library for CodeIgniter to validate form via Ajax.
 * @author	Luigi Mozzillo <luigi@innato.it>
 * @link	http://innato.it
 * @version	1.1.1
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND COPYRIGHT
 * HOLDERS MAKE NO REPRESENTATIONS OR WARRANTIES, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY OR
 * FITNESS FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE
 * OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD PARTY PATENTS,
 * COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.COPYRIGHT HOLDERS WILL NOT
 * BE LIABLE FOR ANY DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL
 * DAMAGES ARISING OUT OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://gnu.org/licenses/>.
 */
class Validation  {

	public $CI;

	protected $data		= array();
	protected $config	= array();
	protected $validate	= TRUE;
	protected $error	= '';

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($config = NULL) {
		$this->CI =& get_instance();

		if (!empty($config))
			$this->initialize($config);
		$this->set_post();	// Default data
	}

	// --------------------------------------------------------------------------

	/**
	 * Initialize library.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function initialize($config) {
		$this->config = $config;
	}

	public function replace_placeholders($message,$replacement_array)
	{


		foreach ( $replacement_array as $key => $value )
		{
			$message = str_replace ( $key, $value, $message );
		}

		return $message;
	}


	// --------------------------------------------------------------------------

	/**
	 * Set fields data from array.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function set_data($data) {
		$this->data = (array) $data;
	}

	public function get_data() {
		return $this->data;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set fields data from POST.
	 *
	 * @access public
	 * @return void
	 */
	public function set_post() {
		$this->set_data($this->CI->input->post());
	}

	// --------------------------------------------------------------------------

	/**
	 * Set fields data from GET.
	 *
	 * @access public
	 * @return void
	 */
	public function set_get() {
		$this->set_data($this->CI->input->get());
	}

	// --------------------------------------------------------------------------

	/**
	 * Exit due error.
	 *
	 * @access private
	 * @param mixed $error
	 * @param mixed $field (default: NULL)
	 * @return void
	 */
	private function _error($error, $field = NULL) {
		if ($this->validate) {
			$this->error = is_null($field) ? $error : sprintf($error, $field);
			$this->validate = FALSE;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Return error message.
	 *
	 * @access public
	 * @return void
	 */
	public function get_error() {
		return $this->error;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check if form is valid.
	 *
	 * @access public
	 * @return void
	 */
	public function validate_input() {


		if (!$this->validate)
		{
		     $debug_log = array('input_data'=>$this->get_data());
		     throw new EtsyException("Parameters passed were not valid");
		}
	}



	/**
	 * Check if form is valid.
	 *
	 * @access public
	 * @return void
	 */
	public function is_valid() {
		return $this->validate;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set form as invalid by passing the string corresponding error.
	 *
	 * @access public
	 * @param string $error (default: '')
	 * @return void
	 */
	public function set_not_valid($error = '') {
		$this->_error($error);
	}

	// --------------------------------------------------------------------------

	/**
	 * If you pass string parameter and not array, puts it in an array.
	 *
	 * @access private
	 * @param mixed &$param
	 * @return void
	 */
	private function _parse(&$param) {
		if (!is_array($param))
			$param = array($param);
	}

	// Case InSensitive In Array
	private function _in_arrayi($needle, $haystack) {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

	// --------------------------------------------------------------------------

	/**
	 * Check required fields.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function required($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				$this->data[$v] = isset($this->data[$v]) ? trim($this->data[$v]) : '';
				if (empty($this->data[$v]))
					$this->_error($err_msg, $v);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check if email fields are valid.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function email($fields, $err_msg = '') {


		$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;


		if ($err_msg=="")
		{
			$validation_code = "101";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}



		$this->_parse($fields);
		$this->CI->load->helper('email');
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if (!valid_email($this->data[$v]))
					$this->_error($err_msg, $v);
			}
		}
		return $this;
	}



	public function num_csv($fields, $err_msg = '') {


		$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;


		if ($err_msg=="")
		{
			$validation_code = "126";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );
			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}


      $this->_parse($fields);

        foreach ($fields as $v) {
            if ($this->is_valid())
            {
                if (!empty($this->data[$v]))
                {

                	$csv_items = explode(",",$this->data[$v]);

                	foreach($csv_items as $item)
                	{
                		if (!is_numeric($item))
                		{
                			$this->_error($err_msg);
                		}
                	}


                }
            }
        }
        return $this;
	}



	// --------------------------------------------------------------------------

	/**
	 * Check that the fields meet a particular regular expression.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $regexp
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function regexp($fields, $regexp, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if (!empty($this->data[$v]))
					if (!preg_match($regexp, $this->data[$v]))
						$this->_error($err_msg, $v);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check URL fields.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function url($fields, $err_msg = '') {
		$regexp = '/^(https?\:\/\/){0,1}(www\.){0,1}([a-z0-9-_.]+)(\.{1})([a-z]{2,4})$/i';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields are not longer than a defined value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $len
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function maxlen($fields, $len, $err_msg = '') {

		$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;
		$replacements["[[LEN]]"] = $len;

		if ($err_msg=="")
		{
			$validation_code = "102";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}


		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if (!empty($this->data[$v]))
					if (strlen($this->data[$v]) > $len)
						$this->_error($err_msg, $v);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields are not shorter than a defined value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $len
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function minlen($fields, $len, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if (!empty($this->data[$v]))
					if (strlen($this->data[$v]) < $len)
						$this->_error($err_msg);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than letters of the alphabet.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function alpha($fields, $err_msg = '') {
		$regexp = '/^([A-Za-z]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than letters of the alphabet and space.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function alpha_s($fields, $err_msg = '') {
		$regexp = '/^([A-Za-z\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than numbers.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num($fields, $err_msg = '') {


		$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;


		if ($err_msg=="")
		{
			$validation_code = "103";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}



		$regexp = '/^([0-9]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}



	/**
	 * Check that fields do not have characters other than numbers and commas.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_or_list($fields, $err_msg = '') {


		$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;


		if ($err_msg=="")
		{
			$validation_code = "130";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}



		$regexp = '/^([0-9,]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}







    /**
     * Check that easy access direction is correct.
     *
     * @access public
     * @param mixed $fields
     * @param string $err_msg (default: '')
     * @return void
     */
    public function easy_access_direction($fields, $err_msg = '') {

        $CI = & get_instance();
        $replacements = array();
        $replacements["[[FIELDS]]"] = $fields;


        if ($err_msg == "")
        {
        	$validation_code = "132";
        	$error_msg_template = $CI->lang->line("validation_".$validation_code);

        	$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
        }
        else
        {
        	$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
        }
        $this->_parse($fields);

        foreach ($fields as $v)
        {
            if ($this->is_valid())
            {
                if (!empty($this->data[$v]))
                {
                    if ($this->data[$v] != 'enter' && $this->data[$v] != 'exit')
                    {
                        $this->_error($err_msg);
                    }
                }
            }
        }
        return $this;
    }




	public function float($fields, $err_msg = '') {

			$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;


		if ($err_msg=="")
		{
			$validation_code = "119";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}


		$regexp = '/^[-+]?\d*\.?\d*$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	public function password($fields, $err_msg = '') {

				$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;


		if ($err_msg=="")
		{
			$validation_code = "120";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}

		$regexp = '/^(.)*$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}




	public function date($fields, $err_msg = '') {

					$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;


		if ($err_msg=="")
		{
			$validation_code = "121";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}

		$regexp = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	public function time($fields, $err_msg = '') {

					$CI = & get_instance ();
		$replacements = array();
		$replacements["[[FIELDS]]"] = $fields;


		if ($err_msg=="")
		{
			$validation_code = "122";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}


		$regexp = '/^([0-9]{2}):([0-9]{2})(:([0-9]{2}))*$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}



	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than numbers and space.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_s($fields, $err_msg = '') {
		$regexp = '/^([0-9\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than letters and numbers.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function alphanum($fields, $err_msg = '') {
		$regexp = '/^([A-Za-z0-9]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have characters other than letters of the alphabet, numbers and space.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function alphanum_s($fields, $err_msg = '') {
		$regexp = '/^([A-Za-z0-9\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that fields do not have spaces.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function no_spaces($fields, $err_msg = '') {
		$regexp = '/^([^\ ]+)$/';
		return $this->regexp($fields, $regexp, $err_msg);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check if a numeric field is greater than a certain value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $num
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_gt($fields, $num, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				if (!$this->num($v, $err_msg) && $this->data[$v] < $num)
					$this->_error($err_msg, $v);
			}
		}
		return $this;
	}





	// --------------------------------------------------------------------------

	/**
	 * Check if a numeric field is less than a certain value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $num
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function num_lt($fields, $num, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {


				if (!$this->num($v, $err_msg) && $this->data[$v] > $num)
					$this->_error($err_msg, $v);
			}
		}
		return $this;
	}





	// --------------------------------------------------------------------------

	/**
	 * Check that the fields have a date.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_old($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				$match = array();
				if (!preg_match('/^([0-9]{2})([^A-Za-z0-9]{1})([0-9]{2})([^A-Za-z0-9]{1})([0-9]{4})$/', $this->data[$v], $match))
					$this->_error($err_msg, $v);
				elseif (!checkdate($match[3], $match[1], $match[5]))
					$this->_error($err_msg, $v);
			}
		}
		return $this;
	}



	/**
	 * Check that the fields have a time.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function time_old($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				$match = array();
				if (!preg_match('/^((([0]?[1-9]|1[0-2])(:|\.)[0-5][0-9]((:|\.)[0-5][0-9])?( )?(AM|am|aM|Am|PM|pm|pM|Pm))|(([0]?[0-9]|1[0-9]|2[0-3])(:|\.)[0-5][0-9]((:|\.)[0-5][0-9])?))$/', $this->data[$v], $match))
					$this->_error($err_msg, $v);
				elseif (!checkdate($match[3], $match[1], $match[5]))
				$this->_error($err_msg, $v);
			}
		}
		return $this;
	}



	// --------------------------------------------------------------------------

	/**
	 * Check the difference between two dates.
	 *
	 * @access private
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @return void
	 */
	private function date_diff($date_1, $date_2) {
		$d1 = strtotime($this->data[$date_1]);
		$d2 = strtotime($this->data[$date_2]);
		return round(($d1 - $d2)/60/60/24);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that a date is larger than other.
	 *
	 * @access public
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_gt($date_1, $date_2, $err_msg = '') {
		if ($this->is_valid()) {
			if ($this->date_diff($date_1, $date_2, $err_msg) > 0)
				$this->_error($err_msg);
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that a date is smaller than other.
	 *
	 * @access public
	 * @param mixed $date_1
	 * @param mixed $date_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_lt($date_1, $date_2, $err_msg = '') {
		if ($this->is_valid()) {
			if ($this->date_diff($date_1, $date_2, $err_msg) < 0)
				$this->_error($err_msg);
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the fields have a English date.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function date_en($fields, $err_msg = '') {
		$this->_parse($fields);
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				$match = array();
				if (!preg_match('/^([0-9]{4})([^A-Za-z0-9]{1})([0-9]{2})([^A-Za-z0-9]{1})([0-9]{2})$/', $this->data[$v], $match))
					$this->_error($err_msg, $v);
				elseif (!checkdate($match[3], $match[5], $match[1]))
					$this->_error($err_msg, $v);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the fields have a datetime value.
	 *
	 * @access public
	 * @param mixed $fields
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function datetime($fields, $err_msg = '') {
		$this->_parse($fields);
		$exp = '/^([0-9]{4})([\-])([0-9]{2})([\-])([0-9]{2})[\ ]([0-9]{2})[\:]([0-9]{2})[\:]([0-9]{2})$/';
		foreach ($fields as $v) {
			if ($this->is_valid()) {
				$match = array();
				if (!preg_match($exp, $this->data[$v], $match))
					$this->_error($err_msg, $v);
				elseif (!checkdate($match[3], $match[5], $match[1]))
					$this->_error($err_msg, $v);
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the field has been checked.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $checked_value
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function checked($field, $checked_value, $err_msg = '') {
		if ($this->is_valid()) {
			if (strcmp($this->data[$field], $checked_value) != 0)
				$this->_error($err_msg);
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the field has been selected.
	 *
	 * @access public
	 * @param mixed $field
	 * @param string $err_msg (default: '')
	 * @param string $empty_value (default: '')
	 * @return void
	 */
	public function selected($field, $err_msg = '', $empty_value = '') {
		if ($this->is_valid()) {
			if (strcmp($this->data[$field], $empty_value) != 0)
				$this->_error($err_msg);
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check that the two fields are equal.
	 *
	 * @access public
	 * @param mixed $field_1
	 * @param mixed $field_2
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function equal($field_1, $field_2, $err_msg = '') {
		if ($this->is_valid()) {
			if (strcmp($this->data[$field_1], $this->data[$field_2]) != 0)
				$this->_error($err_msg);
		}
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Check the correctness of the field $param through the method $method.
	 *
	 * @access public
	 * @param mixed $param
	 * @param mixed $method
	 * @param mixed $err_msg
	 * @return void
	 */
	public function callback($param, $method, $err_msg) {
		if (!method_exists($this->CI, $method)) {
			$this->_error('Method `'. $method .'()` not exists.');
		} else {
			if ($this->CI->$method($this->data[$param]))
				$this->_error($err_msg);
		}
		return $this;
	}

    /**
     * Check if field has value as mentined in enum.
     *
     * @access public
     * @param $fields - field name
     * @param string $enum
     * @param string $err_msg (default: '')
     * @return void
     */
    public function checkenum($fields, $enum, $err_msg = '') {

        $enum_array = explode("|",strtolower($enum));

		$CI = & get_instance ();
        $replacements = array();
        $replacements["[[FIELDS]]"] = $fields;
        $replacements["[[ENUM]]"] = $enum;

        if ($err_msg=="")
        {
        	$validation_code = "123";
        	$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

        	$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
        }
        else
        {
        	$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
        }



        $this->_parse($fields);

        foreach ($fields as $v) {
            if ($this->is_valid()) {
                if(!in_array(strtolower($this->data[$v]),$enum_array))
                    $this->_error($err_msg, $v);
            }
        }

        return $this;
    }

	/**
	 * Check that credit card number is valid.
	 *
	 * @access public
	 * @param mixed $card_number - card number
	 * @param mixed $type - card type
	 * @param string $err_msg (default: '')
	 * @return void
	 */
	public function validate_creditcard($card_number, $type, $err_msg = '') {

		$CI = & get_instance ();
		$replacements = array();
		$replacements["[[CARD_NUMBER]]"] = $card_number;


		if ($err_msg=="")
		{
			$validation_code = "124";
			$error_msg_template = $CI->lang->line ( "validation_".$validation_code );

			$err_msg = $this->replace_placeholders($error_msg_template, $replacements);
		}
		else
		{
			$err_msg = $this->replace_placeholders($error_msg_template, $err_msg);
		}


		$this->_parse($type);

		foreach ($type as $v)
		{
            if ($this->is_valid())
			{
				$type = isset($this->data[$v]) ? strtolower($this->data[$v]) : '';
                if(empty($type))
				{
                    $this->_error($err_msg);
				}
            }
        }

		$card_number = $this->data[$card_number];

		if($type == "visa")
		{
			$regexPattern = "/^4[0-9]{12}(?:[0-9]{3})?$/";
		}
		else if($type == "discover")
		{
			$regexPattern = "/^6(?:011|5[0-9]{2})[0-9]{12}$/";
		}
		else if($type == "amex" || $type == "american express" || $type == "americanexpress")
		{
			$regexPattern = "/^3[47][0-9]{13}$/";
		}
		else if($type == "mastercard" || $type == "master card" || $type == "mc")
		{
			$regexPattern = "/^5[1-5][0-9]{14}$/";
		}
		else if($type == "diners")
		{
			$regexPattern = "/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/";
		}
		else if($type == "jcb")
		{
			$regexPattern = "/^(?:2131|1800|35\d{3})\d{11}$/";
		}
		else
		{
			$this->_error($err_msg);
			return $this;
		}


		if(!preg_match($regexPattern, $card_number))
		{
			$this->_error($err_msg);
		}


		return $this;
	}

}

/* End of file Validation.php */
/* Location: ./application/libraries/Validation.php */