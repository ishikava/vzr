<?php

/**
 * FormValidation Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	FormValidation
 * @author		ExpressionEngine Dev Team
 * @link		http://www.codeigniter.com/user_guide/libraries/form_validation.html
 *
 * 				Модификация класса под Битрикс
 * @author 		Журавель Дмитрий
 * @link   		http://zhuravel.pro
 */
namespace Vzr\Helpers;

class FormValidation {

    protected $_callback_object;
    protected $_field_data	   = array();
    protected $_config_rules   = array();
    protected $_error_array	   = array();
    protected $_error_messages = array();
    protected $_safe_form_data = false;
    protected $_form_processed = false;
    protected $_request_data   = array();
    protected $_request_method = 'POST';

    protected static $_instance;

    private function __construct() {
    }

    private function __clone() {
    }

    public static function getInstance() {

        if (self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    public function reset(){

        $this->_field_data	   = array();
        $this->_config_rules   = array();
        $this->_error_array	   = array();
        $this->_error_messages = array();
        $this->_safe_form_data = false;
        $this->_form_processed = false;
        $this->_request_data   = array();
        $this->_request_method = 'POST';

        return $this;
    }

    public function init($rules = array(), &$object = false, $method = 'POST'){

        $this->_config_rules = $rules;

        if ( $object !== false )
            $this->setCallbackObject($object);

        $this->setRequestMethod($method);

        return $this;
    }

    public function setConfigRules(array $rules=[])
    {
        $this->_config_rules = $rules;
    }

    public function getConfigRules()
    {
        return $this->_config_rules;
    }

    public function setRequestMethod($method){

        if ( strtolower($method) == 'get' )
            $this->_request_method = 'GET';

        return $this;
    }

    public function setRequestData($data){

        if ( !is_array($data) )
            return $this;

        $this->_request_data = $data;

        return $this;
    }

    public function setCallbackObject(&$object){

        $this->_callback_object =& $object;

        return $this;
    }

    public function addError($field, $message){

        if ( !empty($field) && !empty($message) )
            $this->_error_array[$field] = $message;

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set Rules
     *
     * This function takes an array of field names and validation
     * rules as input, validates the info, and stores it
     *
     *
     *
     */
    public function setRules($field, $label = '', $rules = '', $error_messages = '', $is_array=false) {

        if ( empty($this->_request_data) )
            return $this;

        if ( is_array($field) ) {

            foreach ($field as $row) {

                if ( !isset($row['field']) || !isset($row['rules']) )
                    continue;

                $label = ( ! isset($row['label'])) ? $row['field'] : $row['label'];


                $this->setRules($row['field'], $label, $row['rules'], $row['error_messages'], $row['is_array']);
            }

            return $this;
        }

        if ( !is_string($field) ||  !is_array($rules) || $field == '' )
            return $this;

        // If the field label wasn't passed we use the field name
        $label = ($label == '') ? $field : $label;

        // Is the field name an array?  We test for the existence of a bracket "[" in
        // the field name to determine this.  If it is an array, we break it apart
        // into its components so that we can fetch the corresponding POST data later
        if ( strpos($field, '[') !== false && preg_match_all('/\[(.*?)\]/', $field, $matches) ) {

            // Note: Due to a bug in current() that affects some versions
            // of PHP we can not pass function call directly into it
            $x = explode('[', $field);
            $indexes[] = current($x);

            for ($i = 0, $iMax = count($matches['0']); $i < $iMax; $i++) {
                if ($matches['1'][$i] != '') {
                    $indexes[] = $matches['1'][$i];
                }
            }

            $is_array = true;

        } else if ($is_array == true) {
            $is_array = true;
            $indexes  = array($field);
        } else {
            $indexes  = array();
            $is_array = false;
        }

        $this->_field_data[$field] = array(
            'field'	   		 => $field,
            'label'	   		 => $label,
            'rules'	   	     => $rules,
            'is_array' 		 => $is_array,
            'keys'	   		 => $indexes,
            'postdata' 		 => NULL,
            'error'	   		 => '',
            'error_messages' => $error_messages
            //'set_null' => $set_null
        );

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Установка сообщение об ошибки для поля
     *
     * Lets users set their own error messages on the fly.  Note:  The key
     * name has to match the  function name that it corresponds to.
     *
     * @access	public
     * @param	string
     * @param	string
     * @return	mixed
     */
    public function setErrorMessage($rule, $message) {

        $this->_error_messages = array_merge($this->_error_messages, array($rule => $message));

        return $this;
    }

    // --------------------------------------------------------------------

    public function getError($field = false){

        if ( $field === false )
            return $this->_error_array;

        if ( isset($this->_field_data[$field]['error']) )
            return $this->_field_data[$field]['error'];

        return '';
    }

    // --------------------------------------------------------------------

    /**
     * Run the Validator
     *
     *
     * @access	public
     * @return	bool
     */

    // --------------------------------------------------------------------

    /**
     * Traverse a multidimensional $_POST array index until the data is found
     *
     * @access	private
     * @param	array
     * @param	array
     * @param	integer
     * @return	mixed
     */
    protected function _reduce_array($array, $keys, $i = 0) {

        if ( is_array($array) ) {

            if ( !isset($keys[$i]) )
                return $array;

            if ( isset($array[$keys[$i]]) )
                $array = $this->_reduce_array($array[$keys[$i]], $keys, ($i+1));
            else
                return NULL;

        }

        return $array;
    }

    // --------------------------------------------------------------------

    /**
     * Executes the Validation routines
     *
     * @access	private
     * @param	array
     * @param	array
     * @param	mixed
     * @param	integer
     * @return	mixed
     */
    protected function _execute($row, $rules, $postdata = NULL, $cycles = 0) {

        // If the $_POST data is an array we will run a recursive call
        if ( is_array($postdata) ) {

            foreach ($postdata as $key => $val) {
                $this->_execute($row, $rules, $val, $cycles);
                $cycles++;
            }

            return;
        }

        // --------------------------------------------------------------------

        // If the field is blank, but NOT required, no further tests are necessary
        $callback = false;

        if ( !in_array('required', $rules) && is_null($postdata) ) {

            $return = true;
            // Before we bail out, does the rule contain a callback?
            foreach ($rules as $rule) {

                if ( is_array($rule) )
                    $rule = array_shift($rule);

                if ( preg_match("/(callback_\w+(\[.*?\])?)/", $rule, $match) ) {
                    $return = false;
                    break;
                }

            }

            if ( $return )
                return;


            $callback = true;
            $rules = (array('1' => $match[1]));

        }

        // --------------------------------------------------------------------

        // Isset Test. Typically this rule will only apply to checkboxes.
        if ( is_null($postdata) && $callback == false ) {

            if ( !in_array('isset', $rules, true) && !in_array('required', $rules) )
                return;

            // Set the message type
            $type = (in_array('required', $rules)) ? 'required' : 'isset';

            if ( isset($row['error_messages'][$type]) && !empty($row['error_messages'][$type]) )
                $message = $row['error_messages'][$type];

            else if ( !isset($this->_error_messages[$type]) )
                $message = Loc::getMessage("PB_FORMVALIDATION_02", array('#FIELD#' => $row['label']));

            else
                $message = $this->_error_messages[$type];


            // Save the error message
            $this->_field_data[$row['field']]['error'] = $message;

            if ( !isset($this->_error_array[$row['field']]) )
                $this->_error_array[$row['field']] = $message;

        }

        // --------------------------------------------------------------------

        // Cycle through each rule and run it
        foreach ($rules as $rule) {

            $_in_array = false;

            // We set the $postdata variable with the current data in our master array so that
            // each cycle of the loop is dealing with the processed data from the last cycle
            if ( $row['is_array'] == true && is_array($this->_field_data[$row['field']]['postdata']) ) {

                // We shouldn't need this safety, but just in case there isn't an array index
                // associated with this cycle we'll bail out
                if ( !isset($this->_field_data[$row['field']]['postdata'][$cycles]) )
                    continue;


                $postdata =& $this->_field_data[$row['field']]['postdata'][$cycles];
                $_in_array = true;

            } else
                $postdata =& $this->_field_data[$row['field']]['postdata'];


            // --------------------------------------------------------------------

            $param = false;

            if ( is_array($rule) ) {

                $param = array_slice($rule, 1);
                $rule  = $rule[0];

            }


            // Is the rule a callback?
            $callback = false;

            if ( substr($rule, 0, 9) == 'callback_' ) {

                $rule = substr($rule, 9);
                $callback = true;

            }

            // Call the function that corresponds to the rule
            if ( $callback === true ) {

                if ( !method_exists($this->_callback_object, $rule) )
                    continue;


                // Run the function and grab the result
                //$result = $this->_callback_object->$rule(&$this, $postdata, $param);

                if ( !$param ) {
                    $result = $this->_callback_object->$rule($this, $postdata);
                } else {

                    $args = $param;
                    array_unshift($args, $this, $postdata);
                    $result = call_user_func_array(array(&$this->_callback_object, $rule), $args);

                }

                //if ( !is_bool($result) || $result !== false ){

                // Re-assign the result to the master data array
                // if ( $_in_array == true )
                // 	$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;

                // else
                // 	$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;

                //}

                if ( !is_bool($result) )
                    $postdata = $result;
                elseif ( !$result )
                    unset($postdata);


                // If the field isn't required and we just processed a callback we'll move on...
                if ( !in_array('required', $rules, true) && $result !== false )
                    continue;

            } else {

                if ( !method_exists($this, $rule) ) {

                    // If our own wrapper function doesn't exist we see if a native PHP function does.
                    // Users can use any native PHP function call that has one param.
                    if ( function_exists($rule) ) {

                        $result = $rule($postdata);

                        // if ( $_in_array == true )
                        // 	$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;

                        // else
                        // 	$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                        if ( !is_bool($result) )
                            $postdata = $result;
                        elseif ( !$result )
                            unset($postdata);

                    } else {
                        AddMessage2Log("Unable to find validation rule: ".$rule, 'form_validation');
                    }

                    continue;
                }


                if ( !$param ) {
                    $result = $this->$rule($postdata);
                } else {

                    $args = $param;
                    array_unshift($args, $postdata);
                    $result = call_user_func_array(array(&$this, $rule), $args);

                }


                if ( !is_bool($result) )
                    $postdata = $result;
                elseif ( !$result )
                    unset($postdata);

                // if ( $_in_array )
                // 	$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;

                // else
                // 	$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;

            }

            // Did the rule test negatively?  If so, grab the error.
            if ( !$result ) {

                if ( isset($row['error_messages'][$rule]) && !empty($row['error_messages'][$rule]) )
                    $message = $row['error_messages'][$rule];

                else if ( !isset($this->_error_messages[$rule]) )
                    $message = Loc::getMessage("PB_FORMVALIDATION_RULES_".strtoupper($rule), array('#FIELD#' => $row['label'], '#PARAM#' => $param[0]));

                else
                    $message = $this->_error_messages[$rule];

                if ( empty($message) )
                    $line = Loc::getMessage("PB_FORMVALIDATION_01", array('#FIELD#' => $row['label']));


                // Save the error message
                $this->_field_data[$row['field']]['error'] = $message;

                if ( !isset($this->_error_array[$row['field']])  )
                    $this->_error_array[$row['field']] = $message;

                return;
            }
        }
    }


    // public function val($field = false, $value = false) {

    // 	if ( $field === false )	{

    // 		$return = array();

    // 		foreach ($this->_field_data as $field => $value)
    // 			$return[$field] = $value['postdata'];

    // 		return $return;
    // 	}

    // 	if ( $value !== false ) {

    // 		$this->_field_data[$field]['postdata'];

    // 		return $this;
    // 	}


    // 	if ( !isset($this->_field_data[$field]) )
    // 		return $default;

    // 	return $this->_field_data[$field]['postdata'];

    // }


    // --------------------------------------------------------------------

    public function get($field = false, $default = '')
    {
        if ($field === false) {

            $return = array();

            foreach ($this->_field_data as $f => $v) {
                $return[$f] = $v['postdata'];
            }

            return $return;
        }

        if (
            !empty($default)
            && (
                !isset($this->_field_data[$field])
                || empty($this->_field_data[$field]['postdata'])
            )
        ) {
            return $default;
        }

        return $this->_field_data[$field]['postdata'];
    }


    public function inputSetValue($field = '', $default = ''){

        if ( !isset($this->_request_data[$field]) )
            return $default;


        // If the data is an array output them one at a time.
        //     E.g: form_input('name[]', set_value('name[]');
        if (is_array($this->_request_data[$field]))
            return array_shift($this->_request_data[$field]);

        return $this->_request_data[$field];
    }

    // --------------------------------------------------------------------

    /**
     * Required
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function required($str) {

        if ( !is_array($str) )
            return (trim($str) == '') ? false : true;
        else
            return (!empty($str));

    }

    // --------------------------------------------------------------------

    /**
     * Performs a Regular Expression match test.
     *
     * @access	public
     * @param	string
     * @param	regex
     * @return	bool
     */
    public function regex_match($str, $regex) {

        return ( !preg_match($regex, $str) ) ? false : true;

    }


    /**
     * Проверка, совпадает значение одного поля
     * со значением другого.
     *
     * @access	public
     * @param	string
     * @param	field
     * @return	bool
     */
    public function matches($str, $field) {

        if ( !isset($this->_request_data[$field]) )
            return false;

        $field = $this->_request_data[$field];

        return ($str !== $field) ? false : true;
    }

    /**
     * Проверка, совпадает значения одного поля
     * со значением в указанном массиве.
     *
     * @access	public
     * @param	string
     * @param	array
     * @return	bool
     */
    public function in_array($str, $array) {
        return (bool)in_array($str, $array);
    }



    /**
     * Проверка значения поля на уникальность.
     *
     * @access	public
     * @param	string
     * @param	field
     * @return	bool
     */
    public function is_unique($str, $field) {

        list($table, $field)=explode('.', $field);


        $connection = Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $sql = "
			SELECT $field
			FROM $table
			WHERE $field = '".$sqlHelper->forSql($str)."'
			LIMIT 1
		";

        $result = $connection->query($sql);

        return (int)$result->getSelectedRowsCount() === 0;
    }

    // --------------------------------------------------------------------

    /**
     * Minimum Length
     *
     * @access	public
     * @param	string
     * @param	value
     * @return	bool
     */
    public function min_length($str, $val) {

        if (preg_match("/[^0-9]/", $val))
            return false;

        if (function_exists('mb_strlen'))
            return (mb_strlen($str) < $val) ? false : true;

        return (strlen($str) < $val) ? false : true;

    }

    // --------------------------------------------------------------------

    /**
     * Max Length
     *
     * @access	public
     * @param	string
     * @param	value
     * @return	bool
     */
    public function max_length($str, $val) {

        if ( preg_match("/[^0-9]/", $val) )
            return false;

        if (function_exists('mb_strlen'))
            return (mb_strlen($str) > $val) ? false : true;

        return (strlen($str) > $val) ? false : true;

    }

    // --------------------------------------------------------------------

    /**
     * Exact Length
     *
     * @access	public
     * @param	string
     * @param	value
     * @return	bool
     */
    public function exact_length($str, $val) {

        if (preg_match("/[^0-9]/", $val))
            return false;

        if (function_exists('mb_strlen'))
            return (mb_strlen($str) != $val) ? false : true;

        return (strlen($str) != $val) ? false : true;

    }

    // --------------------------------------------------------------------

    /**
     * Valid Email
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function valid_email($str) {
        return ( !preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str) ) ? false : true;
    }

    // --------------------------------------------------------------------

    /**
     * Валидность Emails
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function valid_emails($str) {

        if ( strpos($str, ',' ) === false )
            return $this->valid_email(trim($str));


        foreach (explode(',', $str) as $email)
            if ( trim($email) != '' && $this->valid_email(trim($email)) === false )
                return false;

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Валидность IP-адреса
     *
     * @access	public
     * @param	string
     * @param	string "ipv4" or "ipv6" to validate a specific ip format
     * @return	string
     */
    public function valid_ip($ip, $which = '') {

        $which = strtolower($which);

        // First check if filter_var is available
        if ( is_callable('filter_var') ) {

            switch ($which) {

                case 'ipv4':
                    $flag = FILTER_FLAG_IPV4;
                    break;

                case 'ipv6':
                    $flag = FILTER_FLAG_IPV6;
                    break;

                default:
                    $flag = '';
                    break;
            }

            return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flag);
        }

        if ( $which !== 'ipv6' && $which !== 'ipv4' ) {

            if (strpos($ip, ':') !== false)
                $which = 'ipv6';

            elseif (strpos($ip, '.') !== false)
                $which = 'ipv4';

            else
                return false;
        }

        $func = '_valid_' . $which;
        return $this->$func($ip);
    }


    /**
     * Validate IPv4 Address
     *
     * Updated version suggested by Geert De Deckere
     *
     * @access	protected
     * @param	string
     * @return	bool
     */
    protected function _valid_ipv4($ip) {

        $ip_segments = explode('.', $ip);

        // Always 4 segments needed
        if ( count($ip_segments) !== 4 )
            return false;

        // IP can not start with 0
        if ( $ip_segments[0][0] == '0' )
            return false;


        // Check each segment
        foreach ($ip_segments as $segment) {
            // IP segments must be digits and can not be
            // longer than 3 digits or greater then 255
            if ( $segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3 )
                return false;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Validate IPv6 Address
     *
     * @access	protected
     * @param	string
     * @return	bool
     */
    protected function _valid_ipv6($str) {

        // 8 groups, separated by :
        // 0-ffff per group
        // one set of consecutive 0 groups can be collapsed to ::

        $groups = 8;
        $collapsed = false;

        $chunks = array_filter(
            preg_split('/(:{1,2})/', $str, NULL, PREG_SPLIT_DELIM_CAPTURE)
        );

        // Rule out easy nonsense
        if ( current($chunks) == ':' OR end($chunks) == ':' )
            return false;


        // PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
        if ( strpos(end($chunks), '.') !== false ) {

            $ipv4 = array_pop($chunks);

            if ( ! $this->_valid_ipv4($ipv4) )
                return false;

            $groups--;
        }

        while ($seg = array_pop($chunks)) {

            if ($seg[0] == ':') {

                if ( --$groups == 0 )
                    return false;	// too many groups

                if ( strlen($seg) > 2 )
                    return false;	// long separator

                if ( $seg == '::' ) {

                    if ( $collapsed )
                        return false;	// multiple collapsed

                    $collapsed = true;
                }

            } elseif ( preg_match("/[^0-9a-f]/i", $seg) OR strlen($seg) > 4 )
                return false; // invalid segment

        }

        return $collapsed OR $groups == 1;
    }

    // --------------------------------------------------------------------

    /**
     * Только латинские буквы
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function alpha($str) {
        return (bool)preg_match("/^([a-zA-Z])+$/i", $str);
    }

    // --------------------------------------------------------------------

    /**
     * Возвращает FALSE, если элемент содержит не только буквы и цифры.
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function alpha_numeric($str) {
        return (bool)preg_match("/^([a-zA-Z0-9])+$/i", $str);
    }

    // --------------------------------------------------------------------

    /**
     * Только буквы, цифры, знак подчеркивания и тире.
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function alpha_dash($str) {
        return (bool)preg_match("/^([a-zA-Z0-9_-])+$/i", $str);
    }


    /**
     * Только латинские и руские буквы
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function alpha_ci($str) {
        return  (bool)preg_match('/^([a-zA-Zа-яА-Я])+$/ui', $str);
    }


    /**
     * Только латинсике и руские буквы и цифры
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function alpha_numeric_ci($str) {
        return (bool)preg_match('/^([a-zA-ZА-Яа-я0-9])+$/ui', $str);
    }

    /**
     * Только латинские/русские буквы, цифры и тире/подчеркивание
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function alpha_dash_ci($str) {
        return (bool)preg_match('/^([a-zA-Zа-яА-Я0-9_-])+$/ui', $str);
    }


    // --------------------------------------------------------------------

    /**
     * Только число
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function numeric($str) {
        return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Is Numeric
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function is_numeric($str) {
        return (bool)is_numeric($str);
    }

    // --------------------------------------------------------------------

    /**
     * Integer
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function integer($str) {
        return (bool)preg_match('/^[\-+]?[0-9]+$/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Decimal number
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function decimal($str) {
        return (bool)preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Меньше чем
     *
     * @access	public
     * @param	string
     * @return	bool
     */

    public function xss_clean($str) {
        return FormValidationSecurity::getInstance()->xss_clean($str);
    }

    // --------------------------------------------------------------------

    /**
     * Convert PHP tags to entities
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public function encode_php_tags($str) {
        return str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
    }

    public function html_clean($str, $allowable_tags = ''){
        return strip_tags($str, $allowable_tags);
    }

    public function trim($str){
        return trim($str);
    }

    public function set_int($str){
        return (int)$str;
    }

    public function str_to_upper($str){

        $char = mb_strtoupper(substr($str,0,2),"utf-8");
        $str[0]=$char[0];
        $str[1]=$char[1];

        return $str;
    }

    public function for_sql($str, $maxLength = 0) {
        return \CDatabase::ForSql($str, $maxLength);
    }

    /**
     * @description Replaces underscore with empty string
     *
     * @param $str
     * @return mixed
     */
    public function underscore_trim($str)
    {
        return str_replace('_', '', $str);
    }
}
