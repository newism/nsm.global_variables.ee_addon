<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NSM Global Variables Model 
 *
 * @package			NsmGlobalVariables
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://ee-garage.com/nsm-global-variables
 * @see				http://codeigniter.com/user_guide/general/models.html
 **/
class Nsm_global_variables_model extends CI_Model {

	/**
	 * The model table
	 * 
	 * @var string
	 */
	private static $table_name = "nsm_global_variables";

	/**
	 * The model table fields
	 * 
	 * @var array
	 */
	private static $table_fields = array(
		"id" 			=> array('type' => 'INT', 'constraint' => '10', 'auto_increment' => TRUE, 'unsigned' => TRUE),
		"entry_id" 		=> array('type' => 'INT', 'constraint' => '10'),
		"channel_id" 	=> array('type' => 'INT', 'constraint' => '10'),
		"site_id" 		=> array('type' => 'INT', 'constraint' => '10')
	);

	/**
	 * Create the model table
	 */
	public static function createTable() {
		$EE =& get_instance();
		$EE->load->dbforge();
		$EE->dbforge->add_field(self::$table_fields);
		$EE->dbforge->add_key('id', TRUE);

		if (!$EE->dbforge->create_table(self::$table_name, TRUE)) {
			show_error("Unable to create table in ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$table_name);
			log_message('error', "Unable to create settings table for ".__CLASS__.": " . $EE->config->item('db_prefix') . self::$table_name);
		}
	}
}