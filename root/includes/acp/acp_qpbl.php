<?php
/**
 *
 * @package acp
 * @copyright (c) 2010 Erik Frèrejean ( N/A )
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
 * @package acp
 */
class acp_qpbl
{
	private $error = array(),
			$id,
			$mode;

	public $u_action;

	public function main($id, $mode)
	{
		global $config, $template, $user;

		$this->id	= $id;
		$this->mode	= $mode;
		$submit		= (isset($_REQUEST['submit'])) ? true : false;

		// acp_board style setup
		$vars = array(
			'legend1'	=> 'QPBL_SETTINGS',
			'qpbl_enable'	=> array('lang' => 'QPBL_ENABLE',	'validate' => 'bool',			'type' => 'radio:yes_no',	'explain' => false),
			'qpbl_display'	=> array('lang' => 'QPBL_DISPLAY',	'method' => 'diplay_select',	'type' => 'select',			'explain' => true)
		);
		
		// Fetch the result
		$this->new_config = $config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;

		// We validate the complete config if whished
		validate_config_vars($vars, $cfg_array, $this->error);

		// Form key
		if ($submit && !check_form_key(__FILE__))
		{
			$this->error[] = $user->lang['FORM_INVALID'];
		}

		if (sizeof($this->error))
		{
			$submit = false;
		}

		// Handle submit
		if ($submit)
		{
			foreach (array_keys($vars) as $k)
			{
				if (!isset($cfg_array[$k]) || strpos($k, 'legend') !== false)
				{
					continue;
				}

				$this->new_config[$k] = $config_value = $cfg_array[$k];
				set_config($k, $config_value);
			}
		}

		// Setup the page
		add_form_key(__FILE__);
		$this->tpl_name = 'acp_board';

		// Common stuff
		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang('ACP_QPBL'),
			'L_TITLE_EXPLAIN'	=> $user->lang('ACP_QPBL_EXPLAIN'),

			'S_ERROR'			=> (sizeof($this->error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $this->error),

			'U_ACTION'			=> $this->u_action
		));

		// Generate the output
		foreach ($vars as $config_key => $_vars)
		{
			if (!is_array($_vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$_vars])) ? $user->lang[$_vars] : $_vars)
				);

				continue;
			}

			$type = explode(':', $_vars['type']);

			$l_explain = '';
			if ($_vars['explain'] && isset($_vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$_vars['lang_explain']])) ? $user->lang[$_vars['lang_explain']] : $_vars['lang_explain'];
			}
			else if ($_vars['explain'])
			{
				$l_explain = (isset($user->lang[$_vars['lang'] . '_EXPLAIN'])) ? $user->lang[$_vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $_vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$_vars['lang']])) ? $user->lang[$_vars['lang']] : $_vars['lang'],
				'S_EXPLAIN'		=> $_vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content,
				)
			);

			unset($vars[$config_key]);
		}
	}

	public function diplay_select($selected_method, $key = '')
	{
		global $user;

		$options = array(
			'title'	=> $user->lang('QPBL_DISPLAY_TITLE'),
			'date'	=> $user->lang('QPBL_DISPLAY_DATE'),
		);

		$result = '';
		foreach ($options as $k => $t)
		{
			$selected	= ($k == $selected_method) ? ' selected = "selected"' : '';
			$result		.= "<option value={$k}{$selected}>{$t}</option>";
		}

		return $result;
	}
}