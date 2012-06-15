<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * NSM Form class
 * 
 * Usage:
 * 
 * public function create_form(){
 * 
 * 	$this->EE =& get_instance();
 * 
 * 	if (!class_exists('Nsm_form')) {
 * 		include('models/nsm/form/nsm_form.php');
 * 	}
 *
 * 	$form = new Nsm_form();
 * 
 * 	return $form->build(
 * 		$this->EE->TMPL->tagdata,
 * 		'your_class::class_method',
 * 		array(
 * 			'secure' => array('secure_param' => 'secure_param_value'),
 * 			'hidden' => array()
 * 		)
 * 	);
 * }
 * 
 * public function process_form_submission(){
 * 
 * 	$this->EE =& get_instance();
 * 
 * 	if (!class_exists('Nsm_form')) {
 * 		include('models/nsm/form/nsm_form.php');
 * 	}
 * 
 * 	$form = new NSM_Form();
 * 
 * 	// Process submission
 * 	$form->processSubmitStart();
 * 	
 * 	// var_dump($form->secure_params);
 * 	
 * 	// Redirect submission
 * 	$form->processSubmitEnd();
 * }
 * 
 * 
 * 
 */

class Nsm_form
{
	
	public $AJAX_REQUEST	= false;
	public $content			= false;

	// Form params
	public $opts = array(
		"action"	=> false,
		"enctype"	=> false,
		"secure"	=> true
	);

	// Hidden params displayed in the form itself
	public $hidden_params = array(
		"return"		=> false,
		"ajax_return"	=> false,
		"ACT"			=> false
	);

	// Secure params added to the DB
	var $secure_params = array();

	// Form attributes, parsed from tag params
	var $form_attrs = array();

	static $paramsTable = "nsm_form_params";
	static $paramsTableFields = array(
		"hash" => array(),
		"data" => array(),
		"created_at" => false
	);


	/**
	 * Construct the Form object
	 */
	public function __construct()
	{
		$EE =& get_instance();
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
			$this->AJAX_REQUEST = true;
		}
		
		if (defined('SITE_ID') == false) {
			define('SITE_ID', $EE->config->item('site_id'));
		}
	}

	// ===============================
	// = FORM HTML BUILDER METHODS   =
	// ===============================

	/**
	 * Build the form HTML
	 * 
	 * @access public
	 * @param string $content The form content
	 * @param array $opts The form options
	 * @param array $params The form params
	 * @return string The form HTML
	 */
	public function build($content, $opts = array(), array $params = array())
	{
		$EE				=& get_instance();
		$this->content	= $content;

		// Build the form options
		if (is_string($opts)) {
			$opts = array("ACT" => $opts);
		}

		$this->opts = array_merge($this->opts, $opts);

		if (isset($this->opts["ACT"])) {
			$parts = explode("::", $this->opts["ACT"]);
			$this->hidden_params["ACT"] = $EE->functions->fetch_action_id($parts[0], $parts[1]);
		}

		// Build the hidden params
		if (isset($params["hidden"])) {
			$this->hidden_params = array_merge($this->hidden_params, $params["hidden"]);
		}

		// Build the secure params
		if (isset($params["secure"])) {
			$this->secure_params = array_merge($this->secure_params, $params["secure"]);
		}

		if (!empty($this->secure_params)) {
			$this->hidden_params["params_id"] = $this->_saveParamsToDB();
		}

		// parse the tag params
		$this->_parseTagParams();

		// build the form tag
		$data					= $this->opts;
		$data["hidden_fields"]	= $this->hidden_params;
		$r						= $EE->functions->form_declaration($data);

		// add all the form params
		foreach ($this->form_attrs as $key => $value) {
			$r = str_replace("<form", '<form '.$key.'="'.htmlspecialchars($value).'"', $r);
		}

		$r = $r . $this->content . "</form>";
		return $r;
	}

	/**
	 * Parse the template tag params
	 * 
	 * @access private
	 */
	private function _parseTagParams()
	{
		$EE =& get_instance();
		foreach ($EE->TMPL->tagparams as $key => $value) {
			switch ($key) {
				case ($key == 'return' || $key == 'ajax_return') :
					$this->hidden_params[$key] = $this->_buildReturnURL($value);
					break;
				case (strncmp($key, 'form:', 5) == 0) :
					$this->form_attrs[substr($key, 5)] = $value;
					break;
				case (strncmp($key, 'hidden:', 7) == 0) :
					$this->form_attrs[substr($key, 7)] = $value;
					break;
				/*case (!array_key_exists($key, $this->secure_params)) :
					$this->hidden_params[$key] = $value;
					break;*/
			}
		}
	}

	/**
	 * Save the forms secure params to the db and return a hash / key
	 * 
	 * @access private
	 * @return string The hash / key id for the DB row
	 */
	private function _saveParamsToDB()
	{
		$EE		=& get_instance();
		$hash	= $EE->functions->random('alpha', 25);
		$expiry	= $EE->localize->now - 7200;
		$row	= array(
			"data"			=> serialize($this->secure_params),
			"created_at"	=> $EE->localize->now,
			"hash"			=> $hash,
			"site_id"		=> SITE_ID
		);
		
		$EE->db->where('created_at < ', $expiry)->delete(self::$table_name);
		// insert params into DB
		$EE->db->insert(self::$table_name, $row);
		return $hash;
	}

	/**
	 * Build the return param.
	 * 
	 * The return param can accept template_group/template, whole URLS or EE {path=} variables.
	 * This method turns the URL into a fully qualified URL
	 * 
	 * @access private
	 * @param $str string The raw return string before processing
	 * @return string The fully qualified URL
	 */
	private function _buildReturnURL($str)
	{
		$EE =& get_instance();
		if (preg_match( "/".LD."\s*path=(.*?)".RD."/", $str, $match )) {
			$str = $EE->functions->create_url( $match['1'] );
		} elseif (!preg_match( "#https?:\/\/#", $str)) {
			$str = $EE->functions->create_url($str);
		}
		return $str;
	}

	// ===============================
	// = FORM SUBMISSION METHODS     =
	// ===============================

	/**
	 * Start the form submission processing
	 *
	 * 1. Checks for a secure form submission
	 * 2. Loads in the params from the db
	 * 
	 * @access public
	 */
	function processSubmitStart()
	{
		$EE =& get_instance();
		// secure forms
		if ($EE->config->item('secure_forms') == 'y') {
			$hash	= $EE->input->post('XID');
			$query	= $EE->db->get_where("security_hashes", array("hash" => $hash), 1);

			if ($query->num_rows == 0) {
				$EE->output->fatal_error($EE->lang->line('invalid_action'));
			}
		}

		// get the secure params from the db and load into the form object
		if ($hash = $EE->input->post("params_id")) {
			$query = $EE->db->get_where(self::$table_name, array('hash' => $hash), 1);
			if ($query->num_rows > 0) {
				$row					= $query->row_array();
				$this->secure_params	= unserialize($row["data"]);
			}
		}
	}

	/**
	 * End the form submission processing
	 *
	 * 1. Redirect the user based on the submission params
	 * 
	 * @access public
	 * @param $redirect bool Redirect the user or not
	 */
	function processSubmitEnd($redirect = true)
	{
		$EE =& get_instance();
		if ($redirect) {
			// get the return URL and redirect
			// Ajax, redirect straight away without deleting the hash?
			if ($this->AJAX_REQUEST && $EE->input->get_post('ajax_return')) {
				$EE->functions->redirect($EE->input->get_post('ajax_return'));
			} elseif ($EE->input->get_post('return')) { // Return param
				$return = $EE->input->get_post('return');
			} elseif ($EE->input->get_post('RET')) { // Default return param?
				$return = $EE->input->get_post('RET');
			} else { // site url
				$return = $EE->config->item('site_url');
			}

			if (preg_match( "/".LD."\s*path=(.*?)".RD."/", $return, $match)) {
				$return	= $EE->functions->create_url($match['1']);
			}

			// If everything is successful then delete the submission hash
			if ($EE->config->item('secure_forms') == 'y') {
				$hash = $EE->input->post('XID');
				$EE->db->where('hash', $hash)->delete('security_hashes');
			}

			$EE->functions->redirect($return);
		}
	}
	
	/**
	 * The model table
	 * 
	 * @var string
	 */
	private static $table_name = "nsm_form_prefs";

	/**
	 * The model table fields
	 * 
	 * @var array
	 */
	private static $table_fields = array(
		"id" 			=> array('type' => 'INT', 'constraint' => '10', 'auto_increment' => TRUE, 'unsigned' => TRUE),
		"hash" 			=> array('type' => 'VARCHAR', 'constraint' => '255'),
		"created_at" 	=> array('type' => 'INT', 'constraint' => '10'),
		"site_id" 		=> array('type' => 'INT', 'constraint' => '10'),
		"data" 			=> array('type' => 'TEXT')
	);

	/**
	 * Create the model table
	 */
	public static function createTable()
	{
		$EE =& get_instance();
		$EE->load->dbforge();
		$EE->dbforge->add_field(self::$table_fields);
		$EE->dbforge->add_key('id', true);

		if (!$EE->dbforge->create_table(self::$table_name, true)) {
			show_error("Unable to create table in ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$table_name);
			log_message('error', "Unable to create settings table for ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$table_name);
		}
	}
	
}