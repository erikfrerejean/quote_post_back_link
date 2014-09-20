<?php
/**
 *
 * info_acp_qpbl [English]
 *
 * @package language
 * @author Erik Frèrejean (Erik Frèrejean) erikfrerejean@phpbb.com
 * @copyright (c) 2012 Erik Frèrejean ( N/A )
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_QPBL'			=> 'Quote Post Back Link',
	'ACP_QPBL_EXPLAIN'	=> 'Use this page to configure QPBL',
	'ACP_QPBL_SETTINGS'	=> 'QPBL Settings',

	'QPBL_DISPLAY'			=> 'QPBL display',
	'QPBL_DISPLAY_EXPLAIN'	=> 'Select which format will be used for the backlink',
	'QPBL_DISPLAY_TITLE'	=> 'Post title',
	'QPBL_DISPLAY_DATE'		=> 'Post date',
	'QPBL_ENABLE'			=> 'Enable QPBL',
	'QPBL_L_CASE'			=> 'Titles to lowercase',
	'QPBL_L_CASE_EXPLAIN'	=> 'By setting this option the MOD will force the topic titles that are used in the quote to be all lowercase. Set to `no` if you want to use the original case.',
	'QPBL_SETTINGS'			=> 'QPBL Settings',
));

// Install
$lang = array_merge($lang, array(
	'INSTALL_QPBL'				=> 'Install Quote Post Back Link',
	'INSTALL_QPBL_CONFIRM'		=> 'Are you sure that you want to install QPBL?',
	'UPDATE_QPBL'				=> 'Update Quote Post Back Link',
	'UPDATE_QPBL_CONFIRM'		=> 'Are you sure that you want to update QPBL?',
	'UNINSTALL_QPBL'			=> 'Uninstall Quote Post Back Link',
	'UNINSTALL_QPBL_CONFIRM'	=> 'Are you sure that you want to uninstall QPBL?',
));
