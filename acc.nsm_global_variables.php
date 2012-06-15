<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require PATH_THIRD.'nsm_global_variables/config.php';

/**
 * NSM Global Variables Accessory
 *
 * @package			NsmGlobalVariables
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com> - Technical Director, Newism
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://ee-garage.com/nsm-global-variables
 * @see				http://expressionengine.com/public_beta/docs/development/accessories.html
 */

class Nsm_global_variables_acc 
{
	public $id				= NSM_GLOBAL_VARIABLES_ADDON_ID;
	public $version			= NSM_GLOBAL_VARIABLES_VERSION;
	public $name			= NSM_GLOBAL_VARIABLES_NAME;
	public $description		= 'Accessory for NSM Global Variables.';
	public $sections		= array();

	function set_sections()
	{
		$this->id .= "_acc";
		$this->sections['Title'] = "Content";
	}
}