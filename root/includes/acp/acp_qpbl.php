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
 * @package acp
 */
class acp_qpbl
{
	private $error = array();
	private $id;
	private $mode;

	private $new_config;

	private $config;
	private $template;
	private $user;

	public $u_action;

	public function __construct()
	{
		global $config, $template, $user;

		$this->config	= $config;
		$this->template	= $template;
		$this->user		= $user;
	}

	public function main($id, $mode)
	{
		$this->id	= $id;
		$this->mode	= $mode;
		$submit		= (isset($_REQUEST['submit'])) ? true : false;

		// acp_board style setup
		$vars = array(
			'legend1'	=> 'QPBL_SETTINGS',
			'qpbl_enable'	=> array('lang' => 'QPBL_ENABLE',	'validate' => 'bool',			'type' => 'radio:yes_no',	'explain' => false),
			'qpbl_display'	=> array('lang' => 'QPBL_DISPLAY',	'method' => 'diplay_select',	'type' => 'select',			'explain' => true),
			'qpbl_l_case'	=> array('lang' => 'QPBL_L_CASE',	'validate' => 'bool',			'type' => 'radio:yes_no',	'explain' => true),
		);
		
		// Fetch the result
		$this->new_config = $this->config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;

		// We validate the complete config if whished
		validate_config_vars($vars, $cfg_array, $this->error);

		// Form key
		if ($submit && !check_form_key(__FILE__))
		{
			$this->error[] = $this->user->lang['FORM_INVALID'];
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

				$this->new_config[$k] = $this->config_value = $cfg_array[$k];
				set_config($k, $this->config_value);
			}
		}

		// Setup the page
		add_form_key(__FILE__);
		$this->tpl_name = 'acp_board';

		// Common stuff
		$this->template->assign_vars(array(
			'L_TITLE'			=> $this->user->lang('ACP_QPBL'),
			'L_TITLE_EXPLAIN'	=> $this->user->lang('ACP_QPBL_EXPLAIN'),

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
				$this->template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($this->user->lang[$_vars])) ? $this->user->lang[$_vars] : $_vars)
				);

				continue;
			}

			$type = explode(':', $_vars['type']);

			$l_explain = '';
			if ($_vars['explain'] && isset($_vars['lang_explain']))
			{
				$l_explain = (isset($this->user->lang[$_vars['lang_explain']])) ? $this->user->lang[$_vars['lang_explain']] : $_vars['lang_explain'];
			}
			else if ($_vars['explain'])
			{
				$l_explain = (isset($this->user->lang[$_vars['lang'] . '_EXPLAIN'])) ? $this->user->lang[$_vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $_vars);

			if (empty($content))
			{
				continue;
			}

			$this->template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($this->user->lang[$_vars['lang']])) ? $this->user->lang[$_vars['lang']] : $_vars['lang'],
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
		$options = array(
			'title'	=> $this->user->lang('QPBL_DISPLAY_TITLE'),
			'date'	=> $this->user->lang('QPBL_DISPLAY_DATE'),
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
