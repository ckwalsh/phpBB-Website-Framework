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
	 * Main function
	 */
	public function main()
	{
		$this->page_tpl = 'site_index.html';
		$this->page_title = 'Creating Communites Worldwide';
		
		// blah
		
		return;
	}
}

// EOF