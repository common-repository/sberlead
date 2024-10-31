<?php
/*
Plugin Name: СберЛид
Plugin URI: https://sberlead.ru/
Description: Добавление на сайт кода "СберЛид".
Version: 2.0
Author: Денис Денисов, "СберЛид"
Author URI: https://sberlead.ru
*/

require('admin_panel.php');

class SberLead
{
	var $admin;
	var $options;
	var $options_default = array(
		'script' => '',
		'position' => 'header',
		'mode' => 'all',
	);

	function __construct()
	{
		add_action('init', array($this, 'initial'));
	}

	public static function basename()
	{
		return plugin_basename(__FILE__);
	}

	public function initial()
	{
		$this->options = array_merge(
			$this->options_default,
			(array) get_option('sberlead', array())
		);

		if (defined('ABSPATH') && is_admin()) $this->admin = new SberLeadAdmin();

		// Выводим счетчик в панели администратора
		if (defined('ABSPATH') && is_admin()) {
			add_action('admin_footer', array(&$this, 'add_in_head'));
		}

		add_action('wp_head', array($this, 'add_in_head'), 5);
	}

	// Подготабливаем код для вывода в панели администратора
	function add_in_head()
	{
		if (!empty($this->options['script'])) echo $this->options['script'];
	}
}

$sberlead = new SberLead();
