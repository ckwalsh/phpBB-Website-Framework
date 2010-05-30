<?php
/**
 *
 * @package phpBB Website Framework
 * @version $Id$
 * @copyright (c) 2010 websyntax.net
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

class page
{
	/*
	 * Page template
	 */
	public $page_tpl = '';
	
	/*
	 * Page title
	 */
	public $page_title = '';

	/*
	 * Should the page be cached?
	 */
	public $cached = true;

	/*
	 * Does this page accept parameters?
	 */
	public $params = false;

	/*
	 * Main function
	 */
	public function main($params = array())
	{
		$this->page_tpl = 'page.html';
		$this->page_title = 'Creating Communites right here';
		
		if (!empty($params))
		{
			define('NO_CACHE', true);
		}

		// blah
		
		return;
	}
}

// EOF
