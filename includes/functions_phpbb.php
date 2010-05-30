<?php
/**
 *
 * @package phpBB Website Framework
 * @version $Id$
 * @copyright (c) 2010 websyntax.net
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('IN_WEBSITE') || !defined('IN_PHPBB'))
{
	exit;
}
 
class phpbb
{
	public static $user;
	
	public static $template;
	
	public static $auth;
	
	public static $cache;
	
	public static $db;
	
	public static $config;
	
	/*
	 * Constructor of sorts
	 */
	public static function initialize()
	{
		global $user, $template, $auth, $cache, $db, $config;
		
		self::$user		= &$user;
		self::$template	= &$template;
		self::$auth		= &$auth;
		self::$cache	= &$cache;
		self::$db		= &$db;
		self::$config	= &$config;
		self::$config	= &$config;
	}
}

// EOF