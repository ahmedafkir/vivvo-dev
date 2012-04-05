<?php
/* =============================================================================
 * $Revision: 5385 $
 * $Date: 2010-05-25 11:51:09 +0200 (Tue, 25 May 2010) $
 *
 * Vivvo CMS v4.5.2r (build 6084)
 *
 * Copyright (c) 2010, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * =============================================================================
 */

	/**
	 * Chart data provider base class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	abstract class vivvo_chart_data_provider {

		/**
		 * Generates chart data
		 *
		 * @param	array	$params		Array of data selection parameters
		 * @return	array
		 */
		abstract public function get_chart_data(array $params = array());

		/**
		 * Generates chart settings
		 *
		 * @param	array	$params		Array of data selection parameters
		 * @return	array
		 */
		public function get_chart_settings(array $params = array()) {
			return array();
		}

		/**
		 * Filters params passed trough box_chart
		 *
		 * @param	array	$params		Array of data selection parameters
		 * @return	array
		 */
		public function filter_box_params(array $params = array()) {
			return $params;
		}
	}

	/**
	 * Generic chart class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class vivvo_chart {

		/**
		 * @var	string	Path to amCharts SWF file
		 */
		public static $amcharts_swf = 'flash/amline/amline.swf';

		/**
		 * @var	string	Path to amCharts folder
		 */
		public static $amcharts_path = 'flash/amline/';

		/**
		 * @var	array	Default chart settings
		 */
		public static $defaultSettings = array(
			'data_type' => 'csv',						// string (xml or csv)
			'csv_separator' => ';',						// string (only if type is csv)
#			'skip_rows' => 0,							// int
#			'font' => 'Arial',							// string (Arial, Times New Roman, Tahoma, Verdana)
#			'text_size' => 11,							// int
#			'text_color' => '#000000',					// string (html hex color)
#			'decimals_separator' => ',',				// string
#			'thousands_separator' => ' ',				// string
			'digits_after_decimal' => 2,				// int
#			'scientific_min' => '0.000001',				// string (numeric)
#			'scientific_max' => '1000000000000000',		// string (numeric)
#			'redraw' => false,							// bool
#			'reload_data_interval' => 0,				// int
#			'preloader_on_reload' => false,				// bool
#			'add_time_stamp' => false,					// bool
#			'precision' => 2,							// int
#			'connect' => false,							// bool
#			'hide_bullets_count' => '',					// int
#			'link_target' => '',						// string (_blank, _top, ...)
#			'start_on_axis' => true,					// bool
#			'colors' => '#FF0000,#0000FF,#00FF00,#FF9900,#CC00CC,#00CCCC,#33FF00,#990000,#000066,#555555',	// strign
#			'rescale_on_hide' => true,					// bool
#			'js_enabled' => true,						// bool
#			'background' => array(
#				'color' => '#FFFFFF',					// string (html hex color)
#				'alpha' => 0,							// int (0 - 100)
#				'border_color' => '#000000',			// string (html hex color)
#				'border_alpha' => 0,					// int (0 - 100)
#				'file' => ''							// string
#			),
			'plot_area' => array(
#				'color' => '#FFFFFF',					// string (html hex color)
#				'alpha' => 0,							// int (0 - 100)
#				'border_color' => '#000000',			// string (html hex color)
#				'border_alpha' => 0,					// int (0 - 100)
				'margins' => array(
					'left' => 40,						// int (or percent string)
					'top' => 20,						// int (or percent string)
					'right' => 40,						// int (or percent string)
					'bottom' => 40						// int (or percent string)
				)
			),
#			'scroller' => array(
#				'enabled' => true,						// bool
#				'y' => '',								// int
#				'color' => '#DADADA',					// string (html hex color)
#				'alpha' => 100,							// int (0 - 100)
#				'bg_color' => 'F0F0F0',					// string (html hex color)
#				'bg_alpha' => 100,						// int (0 - 100)
#				'height' => 10							// int
#			),
#			'grid' => array(
#				'x' => array(
#					'enabled' => true,					// bool
#					'color' => '#000000',				// string (html hex color)
#					'alpha' => 15,						// int (0 - 100)
#					'dashed' => false,					// bool
#					'dash_length' => 5,					// int
#					'approx_count' => 4					// int
#				),
#				'y_left' => array(
#					'enabled' => true,					// bool
#					'color' => '#000000',				// string (html hex color)
#					'alpha' => 15,						// int (0 - 100)
#					'dashed' => false,					// bool
#					'dash_length' => 5,					// int
#					'approx_count' => 10,				// int
#					'fill_color' => '#FFFFFF',			// string (html hex color)
#					'fill_alpha' => 0					// int (0 - 100)
#				),
#				'y_right' => array(
#					'enabled' => true,					// bool
#					'color' => '#000000',				// string (html hex color)
#					'alpha' => 15,						// int (0 - 100)
#					'dashed' => false,					// bool
#					'dash_length' => 5,					// int
#					'approx_count' => 10,				// int
#					'fill_color' => '#FFFFFF',			// string (html hex color)
#					'fill_alpha' => 0					// int (0 - 100)
#				)
#			),
#			'values' => array(
#				'x' => array(
#					'enabled' => true,					// bool
#					'rotate' => 0,						// int (0 - 90)
#					'frequency' => 1,					// int
#					'skip_first' => false,				// bool
#					'skip_last' => false,				// bool
#					'color' => '#000000',				// string (html hex color)
#					'text_size' => 11,					// int
#					'inside' => false					// bool
#				),
#				'y_left' => array(
#					'enabled' => true,					// bool
#					'reverse' => false,					// bool
#					'rotate' => 0,						// int (0 - 90)
#					'min' => '',						// int
#					'max' => '',						// int
#					'strict_min_max' => false,			// bool
#					'frequency' => 1,					// int
#					'skip_first' => true,				// bool
#					'skip_last' => false,				// bool
#					'color' => '#000000',				// string (html hex color)
#					'text_size' => 11,					// int
#					'unit' => '',						// string
#					'unit_position' => 'right',			// string (left or right)
#					'integers_only' => false,			// bool
#					'inside' => false,					// bool
#					'duration' => ''					// string (ss/mm/hh/DD)
#				),
#				'y_right' => array(
#					'enabled' => true,					// bool
#					'reverse' => false,					// bool
#					'rotate' => 0,						// int (0 - 90)
#					'min' => '',						// int
#					'max' => '',						// int
#					'strict_min_max' => false,			// bool
#					'frequency' => 1,					// int
#					'skip_first' => true,				// bool
#					'skip_last' => false,				// bool
#					'color' => '#000000',				// string (html hex color)
#					'text_size' => 11,					// int
#					'unit' => '',						// string
#					'unit_position' => 'right',			// string (left or right)
#					'integers_only' => false,			// bool
#					'inside' => false,					// bool
#					'duration' => ''					// string (ss/mm/hh/DD)
#				)
#			),
#			'axes' => array(
#				'x' => array(
#					'color' => '#000000',				// string (html hex color)
#					'alpha' => 100,						// int (0 - 100)
#					'width' => 2,						// int
#					'tick_length' => 7					// int
#				),
#				'y_left' => array(
#					'type' => 'line',					// string (line, stacked or 100% stacked)
#					'color' => '#000000',				// string (html hex color)
#					'alpha' => 100,						// int (0 - 100)
#					'width' => 2,						// int
#					'tick_length' => 7,					// int
#					'logarithmic' => false				// bool
#				),
#				'y_right' => array(
#					'type' => 'line',					// string (line, stacked or 100% stacked)
#					'color' => '#000000',				// string (html hex color)
#					'alpha' => 100,						// int (0 - 100)
#					'width' => 2,						// int
#					'tick_length' => 7,					// int
#					'logarithmic' => false				// bool
#				)
#			),
#			'indicator' => array(
#				'enabled' => true,						// bool
#				'zoomable' => true,						// bool
#				'color' => '#BBBB00',					// string (html hex color)
#				'line_alpha' => 100,					// int (0 - 100)
#				'selection_color' => '#BBBB00',			// string (html hex color)
#				'selection_alpha' => 25,				// int (0 - 100)
#				'x_balloon_enabled' => true,			// bool
#				'x_balloon_text_color' => '#000000'		// string (html hex color)
#			),
#			'baloon' => array(
#				'enabled' => true,						// bool
#				'only_one' => false,					// bool
#				'on_off' => true,						// bool
#				'color' => '',							// string (html hex color)
#				'alpha' => '',							// int (0 - 100)
#				'text_color' => '',						// string (html hex color)
#				'text_size' => 11,						// int
#				'max_width' => '',						// int
#				'corner_radius' => 0,					// int
#				'border_width' => 0,					// int
#				'border_alpha' => '',					// int
#				'border_color' => ''					// string (html hex color)
#			),
#			'legend' => array(
#				'enabled' => true,						// bool
#				'x' => '',								// int or numeric string (number% or !number)
#				'y' => '',								// int or numeric string (number% or !number)
#				'width' => '',							// int or numeric string (number%)
#				'max_columns' => '',					// int
#				'color' => '#FFFFFF',					// string (html hex code)
#				'alpha' => 0,							// int (0 - 100)
#				'border_color' => '#000000',			// string (html hex code)
#				'border_alpha' => 0,					// int (0 - 100)
#				'text_color' => '#000000',				// string (html hex code)
#				'text_color_hover' => '#BBBB00',		// string (html hex code)
#				'text_size' => 11,						// int
#				'spacing' => 10,						// int
#				'margins' => 0,							// int
#				'graph_on_off' => true,					// bool
#				'reverse_order' => false,				// bool
#				'align' => 'left',						// string (left, right or center)
#				'key' => array(
#					'size' => 16,						// int
#					'border_color' => '',				// string (html hex color)
#					'key_mark_color' => '#FFFFFF',		// string (html hex color)
#				),
#				'values' => array(
#					'enabled' => true,					// bool
#					'width' => 80,						// int
#					'align' => 'right',					// string (left or right)
#					'text' => '{value}'					// string
#				)
#			),
#			'vertical_lines' => array(
#				'width'	=> 0,							// int (0 - 100)
#				'alpha' => 100,							// int (0 - 100)
#				'clustered' => false,					// bool
#				'mask' => true							// bool
#			),
#			'zoom_out_button' => array(
#				'x' => '',								// int or numeric string (number% or !number)
#				'y' => '',								// int or numeric string (number% or !number)
#				'color' => '#BBBB00',					// string (html hex color)
#				'alpha' => 0,							// int (0 - 100)
#				'text_color' => '#000000',				// string (html hex code)
#				'text_color_hover' => '#BBBB00',		// string (html hex code)
#				'text_size' => 11,						// int
#				'text' => 'Show all'					// string
#			),
#			'help' => array(
#				'button' => array(
#					'x' => '',							// int or numeric string (number% or !number)
#					'y' => '',							// int or numeric string (number% or !number)
#					'color' => '#00000',				// string (html hex code)
#					'alpha' => 100,						// int (0 - 100)
#					'text_color' => '#FFFFFF',			// string (html hex code)
#					'text_color_hover' => '#BBBB00',	// string (html hex code)
#					'text_size' => '',					// int
#					'text' => '?'						// string
#				),
#				'baloon' => array(
#					'color' => '#00000',				// string (html hex code)
#					'alpha' => 100,						// int (0 - 100)
#					'width' => 300,						// int
#					'text_color' => '#FFFFFF',			// string (html hex code)
#					'text_size' => '',					// int
#					'text' => ''						// string
#				)
#			),
			'export_as_image' => array(
				'file' => 'export',						// string
#				'target' => '_blank',					// string (_blank, _top...)
#				'x' => 0,								// int or numeric string (number% or !number)
#				'y' => '',								// int or numeric string (number% or !number)
#				'color' => '#BBBB00',					// string (html hex code)
#				'alpha' => 100,							// int (0 - 100)
#				'text_color' => '#000000',				// string (html hex code)
#				'text_size' => 11						// int
			),
#			'error_messages' => array(
#				'enabled' => true,						// bool
#				'x' => 0,								// int or numeric string (number% or !number)
#				'y' => '',								// int or numeric string (number% or !number)
#				'color' => '#BBBB00',					// string (html hex code)
#				'alpha' => 100,							// int (0 - 100)
#				'text_color' => '#FFFFFF',				// string (html hex code)
#				'text_size' => 11						// int
#			)
		);

		/**
		 * Initializes chart class
		 */
		public static function init() {

			self::$defaultSettings['export_as_image']['file'] = make_absolute_url(VIVVO_CHART_URL . '/export');
			self::$amcharts_path = VIVVO_STATIC_URL . self::$amcharts_path;
			self::$amcharts_swf = VIVVO_STATIC_URL . self::$amcharts_swf;
		}

		/**
		 * Returns HTML needed for rendering chart
		 *
		 * @param	mixed	$provider		Data provider class (array('class' => 'ClassName', 'file' => 'classfile.php') or just 'ClassName')
		 * @param	array	$params			Array of data selection parameters
		 * @param	array	$appearance		Appearance settings
		 * @return	string
		 */
		public static function get_html($provider, array $params = array(), array $appearance = array()) {

			$id = md5(serialize($params));

			is_array($_SESSION['vivvo']) or $_SESSION['vivvo'] = array();
			is_array($_SESSION['vivvo']['chart']) or $_SESSION['vivvo']['chart'] = array();

			$_SESSION['vivvo']['chart'][$id] = array(
				'provider' => is_object($provider) ? get_class($provider) : $provider,
				'params' => $params
			);

			$appearance = array_map('json_encode', array_merge(array(
				'alternative_content' => 'You need to upgrade your Flash Player',
				'flash_background' => '#FFFFFF',
				'preloader_color' => '#999999',
				'width' => '520',
				'height' => '400'
			), $appearance));

			$appearance['alternative_content'] = json_decode($appearance['alternative_content'], true);

			$amcharts_path = self::$amcharts_path;
			$amcharts_swf = self::$amcharts_swf;

			$amcharts_path = json_encode($amcharts_path);
			$amcharts_swf = json_encode($amcharts_swf);

			$settings_file = json_encode(urlencode(make_absolute_url(VIVVO_CHART_URL . '/' . $id . '/settings')));
			$data_file = json_encode(urlencode(make_absolute_url(VIVVO_CHART_URL . '/' . $id . '/data')));

			return <<<HTML
<div id="d_$id" style="width:100%;height:100%;position:relative">
	<strong>{$appearance[alternative_content]}</strong>
</div>
<script type="text/javascript">
	// <![CDATA[
	(function(){
		var chart = new SWFObject($amcharts_swf, 'f_$id', {$appearance[width]}, {$appearance[height]}, '8', {$appearance[flash_background]});
		chart.addParam('wmode', 'transparent');
		chart.addVariable('path', $amcharts_path);
		chart.addVariable('settings_file', $settings_file);
		chart.addVariable('data_file', $data_file);
		chart.addVariable('preloader_color', {$appearance[preloader_color]});
		chart.write('d_$id');
	})();
	// ]]>
</script>
HTML;
		}

		/**
		 * Returns contents of settings file for a chart
		 *
		 * @param	vivvo_chart_data_provider	$data_provider		Instance of class implementing Chart data provider interface
		 * @param	array						$params				Array of data selection parameters
		 * @return	string
		 */
		public static function get_settings(vivvo_chart_data_provider $data_provider, array $params = array()) {

			$settings = array_merge(self::$defaultSettings, $data_provider->get_chart_settings($params));

			return 	'<?xml version="1.0" encoding="utf-8"?>' . "\n" .
					'<settings>' .
						self::arrayToXML($settings) .
					'</settings>';
		}

		/**
		 * Returns contents of data file for a chart
		 *
		 * @param	vivvo_chart_data_provider	$data_provider		Instance of class implementing Chart data provider interface
		 * @param	array						$params				Array of data selection parameters
		 * @return	string
		 */
		public static function get_data(vivvo_chart_data_provider $data_provider, array $params = array()) {

			$data = $data_provider->get_chart_data($params);
			$settings = array_merge(self::$defaultSettings, $data_provider->get_chart_settings($params));

			is_array($data) or $data = array();

			return self::arrayToCSV($data, $settings['csv_separator']);
		}

		/**
		 * Handles chart url
		 *
		 * @param	array	$url_array
		 */
		public static function route($url_array) {

			is_array($url_array) or go_404();

			if (count($url_array) == 2 and $url_array[1] == 'export') {

				include VIVVO_FS_INSTALL_ROOT . 'flash/amline/export.php';
				exit;
			}

			count($url_array) == 3 or go_404();

			if (isset($_SESSION['vivvo'], $_SESSION['vivvo']['chart'], $_SESSION['vivvo']['chart'][$url_array[1]])) {

				$chart = $_SESSION['vivvo']['chart'][$url_array[1]];

				$data_provider = false;

				if (is_array($chart['provider'])) {
					$data_provider = self::load_data_provider($chart['provider']);
				}

				$data_provider instanceof vivvo_chart_data_provider or go_404();

				if ($url_array[2] == 'settings') {

					header('Content-Type: text/xml');
					echo self::get_settings($data_provider, $chart['params']);
					exit;

				} elseif ($url_array[2] == 'data') {

					header('Content-Type: text/csv');
					echo self::get_data($data_provider, $chart['params']);
					exit;
				}
			}

			go_404();
		}

		/**
		 * Loads data provider and returns new instance of class
		 *
		 * @param	array	$provider
		 * @return	mixed	vivvo_chart_data_provider or false
		 */
		public static function load_data_provider(array $provider) {

			if (empty($provider['class'])) {
				return false;
			}

			$class = $provider['class'];

			if (empty($provider['file'])) {
				$file = false;
			} else {
				$file = $provider['file'];
			}

			if (!class_exists($class) and $file and is_file(VIVVO_FS_INSTALL_ROOT . $file)) {

				include VIVVO_FS_INSTALL_ROOT . $file;

				if (!class_exists($class)) {
					return false;
				}
			}

			return new $class;
		}

		/**
		 * Converts an array to XML string
		 *
		 * @param	array	$array
		 * @return	string
		 */
		protected static function arrayToXML(array $array, &$attribs = null) {

			$ret = '';

			foreach ($array as $key => $values) {

				if (substr($key, -2) == '[]') {
					$key = substr($key, 0, -2);
					$values = (array)$values;
				} else {
					$values = array($values);
				}

				foreach ($values as $value) {

					if (is_array($value)) {

						$attributes = array();
						$content = self::arrayToXML($value, $attributes);

						if (count($attributes)) {
							$ret .= "<$key";
							foreach ($attributes as $name => $value) {
								$ret .= ' ' . $name . '="' . self::xmlentities($value) . '"';
							}
							$ret .= '>';
						} else {
							$ret .= "<$key>";
						}

						$ret .= $content . "</$key>";

					} else {

						if (is_bool($value)) {
							$value = $value ? 'true' : 'false';
						}

						if ($key[0] == '@') {

							$attribs[substr($key, 1)] = $value;

						} else {

							if (preg_match('/[<>&]/', $value) and !preg_match('/^<!\[CDATA\[.*\]\]>$/', $value)) {
								$value = '<![CDATA[' . $value . ']]>';
							}

							$ret .= "<$key>$value</$key>";
						}
					}
				}
			}

			return $ret;
		}

		/**
		 * Encodes string with xml entities
		 *
		 * @param	string		$string
		 * @return	string
		 */
		protected static function xmlentities($string) {
			return strtr($string, array(
				'<' => '&lt;',
				'>' => '&gt;',
				'&' => '&amp;',
				'"' => '&quot;'
			));
		}

		/**
		 * Converts an array to CSV string
		 *
		 * @param	array	$array
		 * @return	string
		 */
		protected static function arrayToCSV(array $array, $separator = ',', $eol_marker = "\n") {

			$ret = '';

			foreach ($array as $row) {
				$row = (array)$row;
				foreach ($row as $index => $item) {
					$row[$index] = self::escape_csv_item($item, $separator);
				}
				$ret .= implode($separator, $row) . $eol_marker;
			}

			return $ret;
		}

		/**
		 * Escapes string as CSV data item
		 *
		 * @param	mixed	$item
		 * @param	string	$separator
		 * @return	string
		 */
		protected static function escape_csv_item($item, $separator = ',') {

			if (strpos($item, '"') !== false || strpos($item, $separator) !== false) {
				return '"' . strtr($item, '"', '""') . '"';
			}

			return $item;
		}
	}

	class_exists('module') or require VIVVO_FS_FRAMEWORK . 'module.class.php';

	/**
	 * Chart module implementation class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class box_chart extends module {

		/**
		 * @var		array	Array of registered chart data providers
		 */
		private $data_providers = false;

		/**
		 * Generates box output
		 *
		 * @param	array	$params
		 */
		public function generate_output($params = array()) {

			if (is_array($params) and !empty($params['module_params'])) {

				$module_params = $params['module_params'];
				unset($params['module_params']);

				if (is_array($module_params)) {
					$params = array_merge($module_params, $params);
				}
			}

			if (!is_array($params) or empty($params['data_provider'])) {
				return;
			}

			if ($this->data_providers === false) {

				$config = vivvo_lite_site::get_instance()
							->get_configuration()
							->get_configuration_property_list('vivvo_chart');

				if (!empty($config['data_providers'])) {

					$this->data_providers = array();

					foreach ($config['data_providers'] as $name => $definition) {
						$this->data_providers[$name] = unserialize($definition);
					}
				}
			}

			if (empty($this->data_providers[$params['data_provider']])) {
				return;
			}

			if (empty($params['template_string']) and empty($params['template'])) {
				$params['template_string'] = '<vte:value select="{CHART_HTML}"/>';
			}

			$this->set_template($params);

			$chart_params = array();
			$chart_appearance = array();

			foreach ($params as $name => $value) {
				if (!in_array($name, array('module', 'data_provider', 'template', 'template_string'))) {
					if (substr($name, 0, 6) == 'chart_') {
						$chart_appearance[substr($name, 6)] = $value;
					} else {
						$chart_params[$name] = $value;
					}
				}
			}

			$provider = vivvo_chart::load_data_provider($this->data_providers[$params['data_provider']]);

			if ($provider === false) {
				return;
			}

			$chart_html = vivvo_chart::get_html(
				$this->data_providers[$params['data_provider']],
				$provider->filter_box_params($chart_params),
				$chart_appearance
			);

			$this->_template->assign('CHART_HTML', $chart_html);
		}
	}


	/**
	 * Handles chart urls (just a wrapper for vivvo_chart::route)
	 *
	 * @param	&vivvo_lite_site	$sm
	 * @param	array				$url_array
	 */
	function vivvo_chart_url_handler($sm, $url_array) {
		return vivvo_chart::route($url_array);
	}

#EOF