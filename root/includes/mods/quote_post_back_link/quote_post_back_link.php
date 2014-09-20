<?php
/**
 *
 * @author Erik Frèrejean (Erik Frèrejean) erikfrerejean@phpbb.com
 * @copyright (c) 2012 Erik Frèrejean ( N/A )
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

class quote_post_back_link
{
	private $bbcode;
	private $cache;
	private $config;
	private $db;
	private $user;
	private $phpEx;

	private $qpbl_posts_cache;

	private static $link_format = '';

	/**
	 * Setup the object
	 */
	public function __construct(cache $cache, array $config, dbal $db, session $user, $phpEx, bbcode $bbcode)
	{
		$this->bbcode	= $bbcode;
		$this->cache	= $cache;
		$this->config	= $config;
		$this->db		= $db;
		$this->user		= $user;
		$this->phpEx	= $phpEx;

		// Load the cache
		$this->qpbl_posts_cache = $this->cache->get('_qpbl');
	}

	/**
	 * Second parse quote tag
	 * QPBL Style
	 */
	public function bbcode_second_pass_quote($username, $post_id, $quote)
	{
		// Disabled?
		if (empty($this->config['qpbl_enable']))
		{
			return $this->bbcode->bbcode_second_pass_quote($username, $quote);
		}

		// when using the /e modifier, preg_replace slashes double-quotes but does not
		// seem to slash anything else
		$quote		= str_replace('\"', '"', $quote);
		$username	= str_replace('\"', '"', $username);

		// Cache links so that we don't have to call append sid every time
		if (empty(self::$link_format))
		{
			self::$link_format = append_sid(
				generate_board_url() . "/viewtopic.{$this->phpEx}",
				array(
					'f' => '%3$d',
					't' => '%2$d',
					'p' => '%1$d',
					'#' => 'p%1$d'
				)
			);
		}

		// remove newline at the beginning
		if ($quote == "\n")
		{
			$quote = '';
		}

		// See whether this post actually exists
		if ($post_id && ($this->qpbl_posts_cache === false || !isset($this->qpbl_posts_cache[$post_id])))
		{
			$sql_ary = array(
				'SELECT'	=> 'p.post_id, p.topic_id, p.forum_id, p.post_subject, p.post_time',
				'FROM'		=> array(
					POSTS_TABLE => 'p',
				),
				'WHERE'		=> "p.post_id = {$post_id}",
			);
			$sql	= $this->db->sql_build_query('SELECT', $sql_ary);
			$result	= $this->db->sql_query($sql);
			$data	= $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($data !== false)
			{
				$this->qpbl_posts_cache[$data['post_id']] = $data;

				// Cache it
				$this->cache->put('_qpbl', $this->qpbl_posts_cache);
			}
		}

		// Prepare the information
		$replace = array();
		if ($username)
		{
			$replace['$1'] = $username;
		}

		if (!empty($this->qpbl_posts_cache[$post_id]))
		{
			$replace['$2'] = vsprintf(self::$link_format, $this->qpbl_posts_cache[$post_id]);
			if ($this->config['qpbl_display'] == 'date')
			{
				// Need to replace the `L_IN` manually
				$replace[$this->user->lang('IN')]	= strtolower($this->user->lang('ON'));
				$replace['$3'] = $this->user->format_date($this->qpbl_posts_cache[$post_id]['post_time']);
			}
			else
			{
				$replace['$3'] = censor_text($this->qpbl_posts_cache[$post_id]['post_subject']);
			}

			// Force lowercase?
			if (!empty($this->config['qpbl_l_case']))
			{
				$replace['$3'] = strtolower($replace['$3']);
			}
		}

		// Select the correct template switch
		$tpl = '';
		if (sizeof($replace) >= 3)
		{
			$tpl = 'quote_username_post_open';
		}
		else if (empty($replace['$1']) && !empty($replace['$2']) && !empty($replace['$3']))
		{
			$tpl = 'quote_post_open';
		}
		else if (!empty($replace['$1']) && empty($replace['$2']) && empty($replace['$3']))
		{
			$tpl = 'quote_username_open';
		}
		else
		{
			return $this->bbcode->bbcode_tpl('quote_open') . $quote;
		}

		return str_replace(array_keys($replace), $replace, $this->bbcode->bbcode_tpl($tpl)) . $quote;
	}
}
