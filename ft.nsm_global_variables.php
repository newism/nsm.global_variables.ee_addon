<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_global_variables/config.php';

/**
 * NSM Global Variables Fieldtype
 *
 * @package			NsmGlobalVariables
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://ee-garage.com/nsm-global-variables
 * @see				http://expressionengine.com/public_beta/docs/development/fieldtypes.html
 */

class Nsm_global_variables_ft extends EE_Fieldtype
{
	public $addon_id = NSM_GLOBAL_VARIABLES_ADDON_ID;
	
	/**
	 * Field info - Required
	 * 
	 * @access public
	 * @var array
	 */
	public $info = array(
		'version'	=> NSM_GLOBAL_VARIABLES_VERSION,
		'name'		=> NSM_GLOBAL_VARIABLES_NAME
	);

	/**
	 * The fieldtype global settings array
	 * 
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * The field type - used for form field prefixes. Must be unique and match the class name. Set in the constructor
	 * 
	 * @access private
	 * @var string
	 */
	public $field_type = '';

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();
	}



	//----------------------------------------
	// INSTALL FIELDTYPE
	//----------------------------------------

	/**
	 * Install the fieldtype
	 *
	 * @return array The default global settings for the fieldtype
	 */
	public function install()
	{
		return array(
			"setting_1" => false,
			"setting_2" => false
		);
	}



	//----------------------------------------
	// REPLACE FIELD / VARIABLE TAG
	//----------------------------------------

	/**
	 * Replaces the custom field tag
	 * 
	 * @access public
	 * @param $data string Contains the field data (or prepped data, if using pre_process)
	 * @param $params array Contains field parameters (if any)
	 * @param $tagdata mixed Contains data between tag (for tag pairs) false for single tags
	 * @return string The HTML replacing the tag
	 * 
	 */
	public function replace_tag($data, $params = false, $tagdata = false)
	{
		return "Tag content";
	}

	/**
	 * Use this method for displaying the variable value in your templates, using the tags.
	 *
	 * @access public
	 * @param $data string Contains the field data (or prepped data, if using pre_process)
	 * @param $params array Contains field parameters (if any)
	 * @param $tagdata mixed Contains data between tag (for tag pairs) false for single tags
	 * @return string The HTML replacing the tag
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function display_var_tag($data, $params, $tagdata )
	{
		return $this->replace_tag($data, $params, $tagdata);
	}




	//----------------------------------------
	// DISPLAY FIELD / CELL / VARIABLE
	//----------------------------------------

	/**
	 * Takes db / post data and parses it so we have the same info to work with every time
	 *
	 * @access private 
	 * @param $data mixed The data we need to prep
	 * @return array The new array of data
	 */
	private function _prepData($data)
	{
		if (!function_exists('json_decode')) {
			$EE->load->library('Services_json');
		}

		$default_data = array(
			'value_1' => false,
			'value_2' => false
		);

		if (empty($data) || is_null($data)) {
			$data = array();
		} elseif (is_string($data)) {
			$data = json_decode($data, true);
		}

		return $this->_mergeRecursive($default_data, $data);
	}
	
	/**
	 * Display the field in the publish form
	 * 
	 * @access public
	 * @param $data String Contains the current field data. Blank for new entries.
	 * @param $input_name String the input name prefix
	 * @param $field_id String The field id - Low variables
	 * @return String The custom field HTML
	 */
	public function display_field($data, $input_name = false, $field_id = false)
	{

		if (!$field_id) {
			$field_id = $this->field_name;
		}

		if (!$input_name) {
			$input_name = $this->field_name;
		}

		$this->_loadResources();

		$vars = array(
			'data'			=> $this->_prepData($data),
			'title'			=> 'NSM Global Variables',
			'input_prefix'	=> $input_name
		);

		if (APP_VER < '2.1.5') {
			// EE < .2.2.0
			// Use the native CI Loader class
			// We need to to do this becuase this field may have been loaded by Matrix or Low variables
			return $this->EE->load->_ci_load(array(
				'_ci_vars'		=> $vars,
				'_ci_path'		=> PATH_THIRD . $this->addon_id . '/views/fieldtype/field.php',
				'_ci_return'	=> true
			));
		} else {
			$this->EE->load->add_package_path(PATH_THIRD . $this->addon_id);
			return $this->EE->load->view('fieldtype/field', $vars, true);
		}

	}

	/**
	 * Displays the cell - MATRIX COMPATIBILITY
	 * 
	 * @access public
	 * @param $data The cell data
	 * @return string The cell HTML
	 */
	public function display_cell($data)
	{
		return $this->display_field($data, $this->cell_name);
	}

	/**
	 * Displays the Low Variable field
	 * 
	 * @access public
	 * @param $var_data The variable data
	 * @return string The cell HTML
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function display_var_field($var_data)
	{
		return $this->display_field($var_data);
	}



	//----------------------------------------
	// DISPLAY FIELD / CELL / VARIABLE SETTINGS
	//----------------------------------------

	/**
	 * Display a global settings page. The current available global settings are in $this->settings.
	 *
	 * @access public
	 * @return string The global settings form HTML
	 */
	public function display_global_settings()
	{
		return "Global settings";
	}

	/**
	 * Default settngs
	 * 
	 * @access public
	 * @param $settings array The field / cell settings
	 * @return array Labels and form inputs
	 */
	private function _defaultFieldSettings()
	{
		return array(
			"setting_1" => false,
			"setting_2" => false
		);
	}

	/**
	 * Display the settings form for each custom field
	 * 
	 * @access public
	 * @param $settings mixed Not sure what this data is yet :S
	 * @param $field_name mixed The field name="" prefix
	 * @return array Labels and fields
	 */
	private function _displayFieldSettings($settings, $field_name = false)
	{

		if (!$field_name) {
			$field_name = __CLASS__;
		}

		$this->_loadResources();

		/* Field Layout */
		$setting_1 = form_dropdown(
							$field_name . "[setting_1]", 
							array(
								'value_1' => 'Value 1',
							    'value_2' => 'value 2'
							),
							$settings['setting_1']
						);

		/* Field Layout */
		$setting_2 = form_dropdown(
							$field_name . "[setting_2]", 
							array(
								'value_1' => 'Value 1',
							    'value_2' => 'value 2'
							),
							$settings['setting_2']
						);

		$r[] = array("Setting 1", $setting_1);
		$r[] = array("Setting 2", $setting_2);
		return $r;
	}

	/**
	 * Display the settings form for each custom field
	 * 
	 * @access public
	 * @param $field_settings array The field settings
	 */
	public function display_settings($field_settings)
	{
		$field_settings = $this->_mergeRecursive($this->_defaultFieldSettings(), $field_settings);
		$rows			= $this->_displayFieldSettings($field_settings);

		// add the rows
		foreach ($rows as $row) {
			$this->EE->table->add_row($row[0], $row[1]);
		}
	}

	/**
	 * Display Cell Settings - MATRIX
	 * 
	 * @access public
	 * @param $cell_settings array The cell settings
	 * @return array Label and form inputs
	 */
	public function display_cell_settings($cell_settings)
	{
		$cell_settings = $this->_mergeRecursive($this->_defaultFieldSettings(), $cell_settings);
		return $this->_displayFieldSettings($cell_settings, $this->addon_id);
	}

	/**
	 * Display Variable Settings - Low Variables
	 * 
	 * @access public
	 * @param $var_settings array The variable settings
	 * @return array Label and form inputs
	 */
	public function display_var_settings($var_settings)
	{
		$var_settings = $this->_mergeRecursive($this->_defaultFieldSettings(), $var_settings);
		return $this->_displayFieldSettings($var_settings);
	}


	//----------------------------------------
	// SAVE FIELD / CELL / VARIABLE SETTINGS
	//----------------------------------------

	/**
	 * Save the custom field settings
	 * 
	 * @param $data array The submitted post data.
	 * @return array Field settings
	 */
	public function save_settings($data)
	{
		return $field_settings = $this->EE->input->post(__CLASS__);
	}

	/**
	 * Process the cell settings before saving - MATRIX
	 * 
	 * @access public
	 * @param $cell_settings array The settings for the cell
	 * @return array The new settings
	 */
	public function save_cell_settings($cell_settings)
	{
		return $cell_settings = $cell_settings[$this->addon_id];
	}

	/**
	 * Save variable settings = LOW Variables
	 * 
	 * @access public
	 * @param $var_settings The variable settings
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function save_var_settings($var_settings)
	{
		return $this->EE->input->post(__CLASS__);
	}

	//----------------------------------------
	// SAVE FIELD / CELL / VARIABLE
	//----------------------------------------

	/**
	 * Publish form validation
	 * 
	 * @access public
	 * @param $data array Contains the submitted field data.
	 * @return mixed true or an error message
	 */
	public function validate($data)
	{
		return true;
	}

	/**
	 * Saves the field
	 */
	public function save($data)
	{
		if (empty($data)) {
			$data = false;
		} elseif (is_array($data)) {
			$data = json_encode($data);
		}

		return $data;
	}

	/**
	 * Modify the Matrix cell’s post data before it gets saved to the database
	 *
	 * @var $data array The cell’s post data
	 * @return string  A string containing the modified variable data to be saved.
	 * @see http://pixelandtonic.com/matrix/docs/ee2-functions#save_cell
	 */
	public function save_cell($data)
	{
		return $this->save($data);
	}

	/**
	 * Save variable data
	 * Use this method to catch the variable value before saving it to the database.
	 * 
	 * @param $var_data string The posted variable data.
	 * @return string A string containing the modified variable data to be saved.
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function save_var_field($var_data)
	{
		return $this->save($var_data);
	}

	//----------------------------------------
	// PRIVATE HELPER METHODS
	//----------------------------------------

	/**
	 * Merges any number of arrays / parameters recursively, replacing 
	 * entries with string keys with values from latter arrays. 
	 * If the entry or the next value to be assigned is an array, then it 
	 * automagically treats both arguments as an array.
	 * Numeric entries are appended, not replaced, but only if they are 
	 * unique
	 *
	 * PHP's array_mergeRecursive does indeed merge arrays, but it converts
	 * values with duplicate keys to arrays rather than overwriting the value 
	 * in the first array with the duplicate value in the second array, as 
	 * array_merge does. e.g., with array_mergeRecursive, this happens 
	 * (documented behavior):
	 * array_mergeRecursive(array('key' => 'org value'), array('key' => 'new value'));
	 *     returns: array('key' => array('org value', 'new value'));
	 * 
	 * calling: result = array_mergeRecursive_distinct(a1, a2, ... aN)
	 *
	 * @author <mark dot roduner at gmail dot com>
	 * @link http://www.php.net/manual/en/function.array-merge-recursive.php#96201
	 * @access private
	 * @param $array1, [$array2, $array3, ...]
	 * @return array Resulting array, once all have been merged
	 */
	private function _mergeRecursive()
	{
		$arrays = func_get_args();
		$base	= array_shift($arrays);
		if (!is_array($base)) {
			$base = (empty($base) ? array() : array($base));
		}
	
		foreach ($arrays as $append) {
	
			if (!is_array($append)) {
				$append = array($append);
			}
	
			foreach ($append as $key => $value) {
				if (!array_key_exists($key, $base) && !is_numeric($key)) {
					$base[$key] = $append[$key];
					continue;
				}
				if (is_array($value) or is_array($base[$key])) {
					$base[$key] = $this->_mergeRecursive($base[$key], $append[$key]);
				} elseif (is_numeric($key)) {
					if (!in_array($value, $base)) {
						$base[] = $value;
					}
				} else {
					$base[$key] = $value;
				}
			}
		}
	
		return $base;
	}

	/**
	 * Get the current themes URL from the theme folder + / + the addon id
	 * 
	 * @access private
	 * @return string The theme URL
	 */
	private function _getThemeUrl()
	{
		$EE =& get_instance();
		if (!isset($EE->session->cache[$this->addon_id]['theme_url'])) {
			$theme_url = $EE->config->item('theme_folder_url');
			if (substr($theme_url, -1) != '/') {
				$theme_url .= '/';
			}
			$theme_url .= "third_party/" . $this->addon_id;
			$EE->session->cache[$this->addon_id]['theme_url'] = $theme_url;
		}
		return $EE->session->cache[$this->addon_id]['theme_url'];
	}

	/**
	 * Load CSS and JS resources for the fieldtype
	 */
	private function _loadResources()
	{
		if (!isset($this->EE->cache[$this->addon_id]['resources_loaded'])) {
			$theme_url = $this->_getThemeUrl();
			$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/styles/admin.css' type='text/css' media='screen' charset='utf-8' />");
			$this->EE->cp->add_to_foot("<script src='{$theme_url}/scripts/admin.js' type='text/javascript' charset='utf-8'></script>");
			$this->EE->cache[$this->addon_id]['resources_loaded'] = true;
		}
	}

}
//END CLASS