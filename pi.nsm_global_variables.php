<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_global_variables/config.php';

/**
 * NSM Global Variables Plugin
 * 
 * Generally a module is better to use than a plugin if if it has not CP backend
 *
 * @package			NsmGlobalVariables
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://ee-garage.com/nsm-global-variables
 * @see 			http://expressionengine.com/public_beta/docs/development/plugins.html
 */

/**
 * Plugin Info
 *
 * @var array
 */
$plugin_info = array(
	'pi_name'			=> NSM_GLOBAL_VARIABLES_NAME,
	'pi_version'		=> NSM_GLOBAL_VARIABLES_VERSION,
	'pi_author'			=> 'Leevi Graham',
	'pi_author_url'		=> 'http://leevigraham.com/',
	'pi_description'	=> 'Plugin description',
	'pi_usage'			=> "Refer to the included README"
);

class Nsm_global_variables
{

	/**
	 * The return string
	 *
	 * @var string
	 */
	public $return_data = "";

	public function __construct()
	{
		$EE =& get_instance();
		$this->return_data = "NSM Global Variables Output";
	}

}