<?php
/**
 *
 * @package phpBB Website Framework
 * @version $Id$
 * @copyright (c) 2010 websyntax.net
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

define('IN_WEBSITE', true);
define('IN_PHPBB', true);

// Path constants
define('PHPBB_ROOT', 'community/'); // leave the ./ off to ensure the paths that go to the template stay pretty
define('SITE_ROOT', './');
define('PHPEX', substr(strrchr(__FILE__, '.'), 1));

$phpbb_root_path	= PHPBB_ROOT;
$phpEx				= PHPEX;

// Include what we need
include PHPBB_ROOT . 'common.' . PHPEX;
include SITE_ROOT . 'includes/module.' . PHPEX;
include SITE_ROOT . 'includes/functions.' . PHPEX;
include SITE_ROOT . 'includes/functions_phpbb.' . PHPEX;
include SITE_ROOT . 'includes/functions_cute_url.' . PHPEX;

// Start it up
phpbb::initialize();
core::initialize();

// Start phpBB session management
phpbb::$user->session_begin();
phpbb::$auth->acl(phpbb::$user->data);
phpbb::$user->setup();

phpbb::$template->set_custom_template(SITE_ROOT . 'template', 'website');

core::add_breadcrumb('Home', '');

// Load page
$module = new phpbb_page();
$module->load();

// EOF