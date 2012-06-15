<?php

/**
 * Config file for NSM Global Variables
 *
 * @package			NsmGlobalVariables
 * @version			0.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://ee-garage.com/nsm-global-variables
 */

if(!defined('NSM_GLOBAL_VARIABLES_VERSION')) {
	define('NSM_GLOBAL_VARIABLES_VERSION', '0.0.1');
	define('NSM_GLOBAL_VARIABLES_NAME', 'NSM Global Variables');
	define('NSM_GLOBAL_VARIABLES_ADDON_ID', 'nsm_global_variables');
}

$config['name'] 	= NSM_GLOBAL_VARIABLES_NAME;
$config["version"] 	= NSM_GLOBAL_VARIABLES_VERSION;

$config['nsm_addon_updater']['versions_xml'] = 'http://github.com/newism/nsm.example_addon.ee_addon/raw/master/versions.xml';
