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

class phpbb_page
{	
	/*
	 * Constructor
	 */
	public function __construct()
	{
	}

	/*
	 * Load the page
	 */
	public function load()
	{
		$file_parts = core::$url->url;
		$filename = NULL;
		$params = array();

		if(empty($file_parts))
		{	 
			// Give them the site_index if there is nothing in the urls
			$filename = SITE_ROOT . 'pages/site_index.' . PHPEX;
		}	 
		elseif(sizeof($filename) > 0 && $filename[0] == 'site_index')
		{	 
			// Force error if they are trying to get the site_index incorrectly
			$filename = SITE_ROOT . 'pages/error.' . PHPEX;
		}	 
		
		while($filename == NULL && sizeof($file_parts) > 0)
		{	 
			$temp_f = SITE_ROOT . 'pages/' . implode('.', $file_parts) . '.' . PHPEX;
		
			if (file_exists($temp_f))
			{	 
				$filename = $temp_f;
				break;
			}
		
		$params[] = array_pop($file_parts);
		}

		if ($filename == NULL)
		{
			// Can't find the page
			core::msg_handler(404);
		}

		$params = array_reverse($params);

		include $filename;
		$page = new page();
		$page->main($params);

		$cache_page = false;

		if (!defined('NO_CACHE')
			&& $_SERVER['REQUEST_METHOD'] == 'GET'
			&& !isset($_COOKIE['no_cache'])
			&& !file_exists(SITE_ROOT . 'static/' . implode('/', core::$url->url) . '/index.html')
		)
		{
			// We can can/should cache this if we can
			$cache_page = SITE_ROOT . 'static/' . implode('/', core::$url->url) . '/index.html';
			ob_start();
		}
		
		// Output the page
		core::page_header($page->page_title);

		phpbb::$template->set_filenames(array(
			'body'	=> $page->page_tpl,
		));

		core::page_footer();

		if ($cache_page !== false)
		{
			$output = ob_get_contents();
			ob_end_flush();

			if (strpos($output, phpbb::$user->sid) === false)
			{
				@mkdir(dirname($cache_page), 0755, true);
				@file_put_contents($cache_page, $output);
			}
			else
			{
				echo "No caching";
			}

			$output = null;

		}

		return;
	}
}

// EOF
