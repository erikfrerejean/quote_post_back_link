<?php
/**
 *
 * @package acp
 * @author Erik Frèrejean (Erik Frèrejean) erikfrerejean@phpbb.com
 * @copyright (c) 2012 Erik Frèrejean ( N/A )
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * @package module_install
 */
class acp_qpbl_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_qpbl',
			'title'		=> 'ACP_QPBL',
			'version'	=> '2.1.0',
			'modes'		=> array(
				'main'	=> array(
					'title'	=> 'ACP_QPBL',
					'auth'	=> 'acl_a_board',
					'cat'	=> array(
						'ACP_MESSAGES',
					)
				),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
