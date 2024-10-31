<?php

class SberLeadAdmin
{
	function __construct()
	{
		// Добавляем страницу настроек в панель администратора
		add_action('admin_menu', array(&$this, 'admin_menu'));

		// Добавляем в описание плагина ссылку на настройку и формируем поля ввода кода.
		add_filter('plugin_row_meta', 'SberLeadAdmin::plugin_row_meta', 10, 2);
		add_action('admin_init', array(&$this, 'plugin_settings'));
	}

	function admin_menu()
	{
		// Добавляем в меню "Настройки" страницу настроек плагина
		add_options_page(
			'Настройка скрипта "СберЛид"',
			'СберЛид',
			'manage_options',
			'sberlead/setting.php',
			array(&$this, 'options_page_output')
		);
	}

	// Добавление ссылок к описанию плагина
	public static function plugin_row_meta($meta, $file)
	{
		if ($file == SberLead::basename()) {
			// Ссылка на страницу справки
			$meta[] = '<a href="options-general.php?page=sberlead/setting.php">Настройки</a>';
		}
		return $meta;
	}

	/**
	 * Создаем страницу настроек плагина
	 */

	function options_page_output()
	{
?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>

			<form action="options.php" method="POST">
				<?php
				settings_fields('sberlead_group');     // скрытые защитные поля
				do_settings_sections('sberlead_page'); // секции с настройками (опциями).
				submit_button();
				?>
			</form>
		</div>
<?php
	}

	/**
	 * Регистрируем настройки.
	 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
	 */
	function plugin_settings()
	{

		// параметры: $option_group, $option_name, $sanitize_callback
		register_setting('sberlead_group', 'sberlead', array(&$this, 'sanitize_callback'));

		// параметры: $id, $title, $callback, $page
		add_settings_section('sberlead', '', array(&$this, 'display_setting_info'), 'sberlead_page');

		$field_params = array(
			'type'      => 'textarea',
			'id'        => 'script',
			'label_for' => 'script'
		);
		add_settings_field('script', 'Уникальный код клиента:', array(&$this, 'display_settings'), 'sberlead_page', 'sberlead', $field_params);
	}

	// Поясняющее сообщение для секции тестирования и отладки
	function display_setting_info()
	{
		echo '<p>Для работы плагина вам необходимо получить на <a href="https://sberlead.ru"  target="_blank">сайте "СберЛид"</a> уникальный код клиента, и вставить его ниже.</p>';
	}

	/*
	 * Функция отображения полей ввода
	 * Здесь задаётся HTML и PHP, выводящий поля
	 */
	function display_settings($args)
	{
		extract($args);

		$option_name = 'sberlead';

		$o = get_option($option_name);

		switch ($type) {
			case 'text':
				$o[$id] = esc_attr(stripslashes($o[$id]));
				echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";
				echo (isset($args['desc'])) ? '<br /><span class="description">' . $args['desc'] . '</span>' : "";
				break;
			case 'textarea':
				$o[$id] = esc_attr(stripslashes($o[$id]));
				echo "<textarea class='code large-text' cols='30' rows='10' type='text' id='$id' name='" . $option_name . "[$id]'>$o[$id]</textarea>";
				echo (isset($args['desc'])) ? '<br /><span class="description">' . $args['desc'] . '</span>' : "";
				break;
			case 'checkbox':
				$checked = (!empty($o[$id]) && $o[$id] == 'on') ? " checked='checked'" :  '';
				echo "<label><input type='checkbox' id='$id' name='" . $option_name . "[$id]' $checked /> ";
				echo (isset($args['desc'])) ? $args['desc'] : "";
				echo "</label>";
				break;
			case 'checkbox-group':
				echo '<ul style="margin-top: 10px;">';
				foreach ($vals as $v => $l) {
					echo '<li>';
					$checked = (isset($o[$id]) && $o[$id] == $v) ? " checked='checked'" :  '';
					echo "<label><input type='checkbox' id='$id' name='" . $option_name . "[$id]' value='$v' $checked /> ";
					echo ($l != '') ? $l : "";
					echo "</label>";
					echo '</li>';
				}
				echo '<ul>';

				break;
			case 'select':
				echo "<select id='$id' name='" . $option_name . "[$id]'>";
				foreach ($vals as $v => $l) {
					$selected = ($o[$id] == $v) ? "selected='selected'" : '';
					echo "<option value='$v' $selected>$l</option>";
				}
				echo "</select>";
				echo (isset($args['desc'])) ? '<br /><span class="description">' . $args['desc'] . '</span>' : "";
				break;
			case 'radio':
				echo "<fieldset>";
				foreach ($vals as $v => $l) {
					$checked = ($o[$id] == $v) ? "checked='checked'" : '';
					echo "<label><input type='radio' name='" . $option_name . "[$id]' value='$v' $checked />$l</label><br />";
				}
				echo "</fieldset>";
				break;
			case 'info':
				echo '<p>' . $text . '</p>';
				break;
		}
	}

	## Очистка данных
	function sanitize_callback($options)
	{
		// очищаем
		foreach ($options as $name => &$val) {
			if ($name == 'input')
				$val = strip_tags($val);

			if ($name == 'checkbox')
				$val = intval($val);
		}

		//die(print_r( $options ));

		return $options;
	}
}
?>