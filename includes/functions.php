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

class core
{
	public static $url;
	
	/*
	 * Constructor of sorts
	 * Loads what we need to get started
	 */
	public static function initialize()
	{
		self::$url = new cute_url_handler('/');
	}
	
	/*
	 * Unifed website message hanlder
	 */
	public static function msg_handler($ecode = 0, $title = '', $msg = '')
	{
		$errors = array(
			0   => array('title' => '000 Error of errors', 'body' => 'You are seeing this becuase something failed miseraibly with both the website and the error handling. Hit the back button and pretend it never happend.'),
			400	=> array('title' => '400 Bad Request', 'body' => 'Your request cannot be completed, probably due to an error in the URL you requested.'),
			401	=> array('title' => '401 Unauthorised', 'body' => 'Your request cannot be completed, probably due to an error in the URL you requested.'),
			403	=> array('title' => '403 Forbidden', 'body' => 'You are not allowed to access this area.'),
			404 => array('title' => '404 Not Found', 'body' => 'The page you requested does not exist.'),
			500	=> array('title' => '500 Internal Server Error', 'body' => 'The server was unable to complete your request.'),
			502	=> array('title' => '502 Service Unavailable', 'body' => 'The server is experiencing a very high amount of requests and has reached its maximum client limit.'),
			503	=> array('title' => '503 Gateway Timeout', 'body' => 'There was a problem with the underlying network connection, please try refreshing the page or trying again later.'),
		);
		
		if($ecode)
		{
			$ecode = array_key_exists($ecode, $errors) ? $ecode : 0;
			
			$title 	= $errors[$ecode]['title'];
			$msg	= $errors[$ecode]['title'];
		}
		
		phpbb::$template->assign_vars(array(
			'MESSAGE_TITLE'	=> $title,
			'MESSAGE_TEXT'	=> $msg,
		));
		
		core::page_header($title);
		phpbb::$template->set_filenames(array(
			'body'	=> 'message_body.html',
		));
		core::page_footer();
	}

	/*
	 * Add a breadcrumb
	 */	
	public static function add_breadcrumb($title, $url, $params = '')
	{
		if($url == '/' || $url == '')
		{
			$url = array();
		}
		else if(!is_array($url))
		{
			$url = explode('/', $url);
		}
		
		phpbb::$template->assign_block_vars('breadcrumbs', array(
			'TITLE'	=> $title,
			'URL'	=> core::$url->build($url, $params),
		));
		
		return;
	}
	
	public static function static_output($page_header, $page_content)
	{
		phpbb::$template->assign_var('PAGE_HEADER', $page_header);
		
		foreach($page_content as $heading => $paragraph)
		{
			phpbb::$template->assign_block_vars('sections', array(
				'HEADER'	=> $heading, 
				'PARAGRAPH'	=> $paragraph,
			));
		}
		
		return;
	}
	
	/*
	 * Website page header
	 */
	public static function page_header($page_title)
	{
		if (defined('HEADER_INC') || defined('WEB_HEADER_INC'))
		{
			return;
		}
		
		define('WEB_HEADER_INC', true);
		
		// gzip_compression
		if (phpbb::$config['gzip_compress'])
		{
			if (@extension_loaded('zlib') && !headers_sent())
			{
				ob_start('ob_gzhandler');
			}
		}
		
		// Generate logged in/logged out status
		if (phpbb::$user->data['user_id'] != ANONYMOUS)
		{
			$u_login_logout = append_sid(PHPBB_ROOT . 'ucp.' . PHPEX, 'mode=logout', true, phpbb::$user->session_id);
			$l_login_logout = sprintf(phpbb::$user->lang['LOGOUT_USER'], phpbb::$user->data['username']);
		}
		else
		{
			$u_login_logout = append_sid(PHPBB_ROOT . 'ucp.' . PHPEX, 'mode=login');
			$l_login_logout = phpbb::$user->lang['LOGIN'];
		}

		$board_url = generate_board_url() . '/';
		
		$tz = (phpbb::$user->data['user_id'] != ANONYMOUS) ? strval(doubleval(phpbb::$user->data['user_timezone'])) : strval(doubleval(phpbb::$config['board_timezone']));
		
		// Send a proper content-language to the output
		$user_lang = phpbb::$user->lang['USER_LANG'];
		if (strpos($user_lang, '-x-') !== false)
		{
			$user_lang = substr($user_lang, 0, strpos($user_lang, '-x-'));
		}
	
		// Global vars
		phpbb::$template->assign_vars(array(
			'SITENAME'						=> phpbb::$config['sitename'],
			'SITE_DESCRIPTION'				=> phpbb::$config['site_desc'],
			'PAGE_TITLE'					=> $page_title,
			'SCRIPT_NAME'					=> str_replace('.' . PHPEX, '', phpbb::$user->page['page_name']),
			
			'SESSION_ID'		=> phpbb::$user->session_id,
			'ROOT_PATH'			=> PHPBB_ROOT,
			'BOARD_URL'			=> $board_url,
			
			'L_LOGIN_LOGOUT'	=> $l_login_logout,

			'U_MEMBERLIST'			=> append_sid(core::$url->web_root . PHPBB_ROOT . 'memberlist.' . PHPEX),
			'U_LOGIN_LOGOUT'		=> $u_login_logout,
			'U_INDEX'				=> append_sid(core::$url->web_root . PHPBB_ROOT . 'index.' . PHPEX),
			'U_SEARCH'				=> append_sid(core::$url->web_root . PHPBB_ROOT . 'search.' . PHPEX),
			'U_REGISTER'			=> append_sid(core::$url->web_root . PHPBB_ROOT . 'ucp.' . PHPEX, 'mode=register'),
			'U_PROFILE'				=> append_sid(core::$url->web_root . PHPBB_ROOT . 'ucp.' . PHPEX),
			'U_MODCP'				=> append_sid(core::$url->web_root . PHPBB_ROOT . 'mcp.' . PHPEX, false, true, phpbb::$user->session_id),
			'U_FAQ'					=> append_sid(core::$url->web_root . PHPBB_ROOT . 'faq.' . PHPEX),
			'U_SEARCH_SELF'			=> append_sid(core::$url->web_root . PHPBB_ROOT . 'search.' . PHPEX, 'search_id=egosearch'),
			'U_SEARCH_NEW'			=> append_sid(core::$url->web_root . PHPBB_ROOT . 'search.' . PHPEX, 'search_id=newposts'),
			'U_SEARCH_UNANSWERED'	=> append_sid(core::$url->web_root . PHPBB_ROOT . 'search.' . PHPEX, 'search_id=unanswered'),
			'U_SEARCH_UNREAD'		=> append_sid(core::$url->web_root . PHPBB_ROOT . 'search.' . PHPEX, 'search_id=unreadposts'),
			'U_SEARCH_ACTIVE_TOPICS'=> append_sid(core::$url->web_root . PHPBB_ROOT . 'search.' . PHPEX, 'search_id=active_topics'),
			'U_DELETE_COOKIES'		=> append_sid(core::$url->web_root . PHPBB_ROOT . 'ucp.' . PHPEX, 'mode=delete_cookies'),
			'U_TEAM'				=> (phpbb::$user->data['user_id'] != ANONYMOUS && !phpbb::$auth->acl_get('u_viewprofile')) ? '' : append_sid(core::$url->web_root . PHPBB_ROOT . 'memberlist.' . PHPEX, 'mode=leaders'),
			'U_TERMS_USE'			=> append_sid(core::$url->web_root . PHPBB_ROOT . 'ucp.' . PHPEX, 'mode=terms'),
			'U_PRIVACY'				=> append_sid(core::$url->web_root . PHPBB_ROOT . 'ucp.' . PHPEX, 'mode=privacy'),
			'U_RESTORE_PERMISSIONS'	=> (phpbb::$user->data['user_perm_from'] && phpbb::$auth->acl_get('a_switchperm')) ? append_sid(core::$url->web_root . PHPBB_ROOT . 'ucp.' . PHPEX, 'mode=restore_perm') : '',
			'U_FEED'				=> generate_board_url() . '/feed.' . PHPEX,

			'S_USER_LOGGED_IN'		=> (phpbb::$user->data['user_id'] != ANONYMOUS) ? true : false,
			'S_AUTOLOGIN_ENABLED'	=> (phpbb::$config['allow_autologin']) ? true : false,
			'S_BOARD_DISABLED'		=> (phpbb::$config['board_disable']) ? true : false,
			'S_REGISTERED_USER'		=> (!empty(phpbb::$user->data['is_registered'])) ? true : false,
			'S_IS_BOT'				=> (!empty(phpbb::$user->data['is_bot'])) ? true : false,
			'S_USER_LANG'			=> $user_lang,
			'S_USER_BROWSER'		=> (isset(phpbb::$user->data['session_browser'])) ? phpbb::$user->data['session_browser'] : phpbb::$user->lang['UNKNOWN_BROWSER'],
			'S_USERNAME'			=> phpbb::$user->data['username'],
			'S_CONTENT_DIRECTION'	=> phpbb::$user->lang['DIRECTION'],
			'S_CONTENT_FLOW_BEGIN'	=> (phpbb::$user->lang['DIRECTION'] == 'ltr') ? 'left' : 'right',
			'S_CONTENT_FLOW_END'	=> (phpbb::$user->lang['DIRECTION'] == 'ltr') ? 'right' : 'left',
			'S_CONTENT_ENCODING'	=> 'UTF-8',
			'S_TIMEZONE'			=> (phpbb::$user->data['user_dst'] || (phpbb::$user->data['user_id'] == ANONYMOUS && phpbb::$config['board_dst'])) ? sprintf($user->lang['ALL_TIMES'], phpbb::$user->lang['tz'][$tz], phpbb::$user->lang['tz']['dst']) : sprintf(phpbb::$user->lang['ALL_TIMES'], phpbb::$user->lang['tz'][$tz], ''),

			'S_LOGIN_ACTION'		=> ((!defined('ADMIN_START')) ? append_sid(PHPBB_ROOT . 'ucp.' . PHPEX, 'mode=login') : append_sid('index.' . PHPEX, false, true, phpbb::$user->session_id)),
			'S_LOGIN_REDIRECT'		=> build_hidden_fields(array('redirect' => str_replace('&amp;', '&', build_url()))),

			'T_THEME_PATH'			=> core::$url->web_root . PHPBB_ROOT. 'styles/' . phpbb::$user->theme['theme_path'] . '/theme',
			'T_TEMPLATE_PATH'		=> core::$url->web_root . PHPBB_ROOT . 'styles/' . phpbb::$user->theme['template_path'] . '/template',
			'T_SUPER_TEMPLATE_PATH'	=> (isset($user->theme['template_inherit_path']) && phpbb::$user->theme['template_inherit_path']) ? core::$url->web_root . 'styles/' . phpbb::$user->theme['template_inherit_path'] . '/template' : core::$url->web_root . 'styles/' . phpbb::$user->theme['template_path'] . '/template',
			'T_IMAGESET_PATH'		=> core::$url->web_root . PHPBB_ROOT . 'styles/' . phpbb::$user->theme['imageset_path'] . '/imageset',
			'T_IMAGESET_LANG_PATH'	=> core::$url->web_root . PHPBB_ROOT . 'styles/' . phpbb::$user->theme['imageset_path'] . '/imageset/' . phpbb::$user->data['user_lang'],
			'T_IMAGES_PATH'			=> core::$url->web_root . PHPBB_ROOT . 'images/',
			'T_SMILIES_PATH'		=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['smilies_path'] . '/',
			'T_AVATAR_PATH'			=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['avatar_path'] . '/',
			'T_AVATAR_GALLERY_PATH'	=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['avatar_gallery_path'] . '/',
			'T_ICONS_PATH'			=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['icons_path'] . '/',
			'T_RANKS_PATH'			=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['ranks_path'] . '/',
			'T_UPLOAD_PATH'			=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['upload_path'] . '/',
			'T_STYLESHEET_LINK'		=> (!phpbb::$user->theme['theme_storedb']) ? core::$url->web_root . PHPBB_ROOT . 'styles/' . phpbb::$user->theme['theme_path'] . '/theme/stylesheet.css' : append_sid(core::$url->web_root . PHPBB_ROOT . 'style.' . PHPEX, 'id=' . phpbb::$user->theme['style_id'] . '&amp;lang=' . phpbb::$user->data['user_lang']),
			'T_STYLESHEET_NAME'		=> phpbb::$user->theme['theme_name'],

			'T_THEME_NAME'			=> phpbb::$user->theme['theme_path'],
			'T_TEMPLATE_NAME'		=> phpbb::$user->theme['template_path'],
			'T_SUPER_TEMPLATE_NAME'	=> (isset(phpbb::$user->theme['template_inherit_path']) && phpbb::$user->theme['template_inherit_path']) ? phpbb::$user->theme['template_inherit_path'] : phpbb::$user->theme['template_path'],
			'T_IMAGESET_NAME'		=> phpbb::$user->theme['imageset_path'],
			'T_IMAGESET_LANG_NAME'	=> phpbb::$user->data['user_lang'],
			'T_IMAGES'				=> core::$url->web_root . PHPBB_ROOT . 'images',
			'T_SMILIES'				=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['smilies_path'],
			'T_AVATAR'				=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['avatar_path'],
			'T_AVATAR_GALLERY'		=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['avatar_gallery_path'],
			'T_ICONS'				=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['icons_path'],
			'T_RANKS'				=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['ranks_path'],
			'T_UPLOAD'				=> core::$url->web_root . PHPBB_ROOT . phpbb::$config['upload_path'],
			
			'A_COOKIE_SETTINGS'		=> addslashes('; path=' . phpbb::$config['cookie_path'] . ((!phpbb::$config['cookie_domain'] || phpbb::$config['cookie_domain'] == 'localhost' || phpbb::$config['cookie_domain'] == '127.0.0.1') ? '' : '; domain=' . phpbb::$config['cookie_domain']) . ((!phpbb::$config['cookie_secure']) ? '' : '; secure')),
		));
		
		// application/xhtml+xml not used because of IE
		header('Content-type: text/html; charset=UTF-8');

		header('Cache-Control: private, no-cache="set-cookie"');
		header('Expires: 0');
		header('Pragma: no-cache');

		return;
	}
	
	/*
	 * Website page footer
	 */
	public static function page_footer()
	{
		global $starttime; // one global :(
		
		// Output page creation time
		if (defined('DEBUG'))
		{
			$mtime = explode(' ', microtime());
			$totaltime = $mtime[0] + $mtime[1] - $starttime;

			if (!empty($_REQUEST['explain']) && phpbb::$auth->acl_get('a_') && defined('DEBUG_EXTRA') && method_exists(phpbb::$db, 'sql_report'))
			{
				phpbb::$db->sql_report('display');
			}

			$debug_output = sprintf('Time : %.3fs | ' . phpbb::$db->sql_num_queries() . ' Queries | GZIP : ' . ((phpbb::$config['gzip_compress'] && @extension_loaded('zlib')) ? 'On' : 'Off') . ((phpbb::$user->load) ? ' | Load : ' . phpbb::$user->load : ''), $totaltime);

			if (phpbb::$auth->acl_get('a_') && defined('DEBUG_EXTRA'))
			{
				if (function_exists('memory_get_usage'))
				{
					if ($memory_usage = memory_get_usage())
					{
						global $base_memory_usage;
						$memory_usage -= $base_memory_usage;
						$memory_usage = get_formatted_filesize($memory_usage);

						$debug_output .= ' | Memory Usage: ' . $memory_usage;
					}
				}

				$debug_output .= ' | <a href="' . build_url() . '&amp;explain=1">Explain</a>';
			}
		}

		phpbb::$template->assign_vars(array(
			'DEBUG_OUTPUT'			=> (defined('DEBUG')) ? $debug_output : '',
			'TRANSLATION_INFO'		=> (!empty(phpbb::$user->lang['TRANSLATION_INFO'])) ? phpbb::$user->lang['TRANSLATION_INFO'] : '',

			'U_ACP' => (phpbb::$auth->acl_get('a_') && !empty(phpbb::$user->data['is_registered'])) ? append_sid(PHPBB_ROOT . 'adm/index.' . PHPEX, false, true, phpbb::$user->session_id) : '')
		);
	
		phpbb::$template->display('body');

		// Unload cache, must be done before the DB connection if closed
		if (!empty(phpbb::$cache))
		{
			phpbb::$cache->unload();
		}

		// Close our DB connection.
		if (!empty(phpbb::$db))
		{
			phpbb::$db->sql_close();
		}
		
		(empty($config['gzip_compress'])) ? @flush() : @ob_flush();

	}
}

// EOF
