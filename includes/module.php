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
		$filename = implode('.', core::$url->url);
		
		if($filename == 'site_index')
		{
			// Force error if they are trying to get the site_index incorrectly
			$filename = 'error';
		}
		else if(empty($filename))
		{
			// Give them the site_index if there is nothing in the urls
			$filename = 'site_index';
		}
		
		$filename_full = SITE_ROOT . "pages/{$filename}." . PHPEX;
		
		if(!file_exists($filename_full))
		{
			core::msg_handler(404);
		}
		
		include $filename_full;
		$page = new page();
		$page->main();
		
		// Output the page
		core::page_header($page->page_title);

		phpbb::$template->set_filenames(array(
			'body'	=> $page->page_tpl,
		));

		core::page_footer();
		
		return;
	}
}

// EOF