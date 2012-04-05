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

	class_exists('HTTP_Request2') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/PEAR/HTTP/Request2.php';
	class_exists('VivvoXMLParser') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvoxml.php';
	class_exists('vivvo_chart') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_chart.class.php';
	class_exists('module') or require VIVVO_FS_FRAMEWORK . 'module.class.php';

	/**
	 * vivvo_ga class - Google Analytics Data Export API Client
	 *
	 * @copyright	Spoonlabs
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	*/
	class vivvo_ga {

		/**
		 * @var	string	HTTP_Request2 adapter class to use
		 */
		public static $request_adapter = 'socket';

		/**
		 * Checks if minimum system requirements are met for vivvo_ga to function properly
		 *
		 * @param	&string	$missing	If function returns false passed variable will hold info about missing component
		 * @return	bool
		 */
		public static function is_supported(&$missing = null) {
			$missing = 'php';
			if (version_compare(PHP_VERSION, '5.1.4', '>=') !== false) {
				$missing = 'curl';
				if (extension_loaded('curl')) {
					return !($missing = false);
				}
				$missing = 'socket';
				if (function_exists('stream_socket_client')) {
					$missing = 'ssl';
					if (in_array('ssl', stream_get_transports())) {
						return !($missing = false);
					}
				}
			}
			return false;
		}

		/**
		 * Defines global GA-specific constants
		 */
		public static function init() {

			define('VIVVO_GA_SUPPORTED', vivvo_ga::is_supported());

			if (VIVVO_GA_SUPPORTED and defined('VIVVO_GA_ENABLED') and VIVVO_GA_ENABLED) {

				if (defined('VIVVO_GA_CODE') and VIVVO_GA_CODE) {
					define('VIVVO_ANALYTICS_TRACKER_ID', preg_replace('/^(UA-)?(.)/', 'UA-$2', VIVVO_GA_CODE));
				}

				$value = true;
				foreach (array('VIVVO_GA_EMAIL', 'VIVVO_GA_PASSWORD', 'VIVVO_GA_PROFILEID') as $param) {
					if (!defined($param) || !constant($param)) {
						$value = false;
						break;
					}
				}
				$value and define('VIVVO_ANALYTICS_ENABLED', true);
			}
		}

		/**
		 * @var	string	Email address used for authentification
		 */
		protected $email;

		/**
		 * @var	string	Password used for authentification
		 */
		protected $passwd;

		/**
		 * @var	string	Authorization code
		 */
		protected $auth_code;

		/**
		 * @var	VivvoXMLParser	XML Parser class
		 */
		protected $xml_parser;

		/**
		 * @var string	Profile id
		 */
		protected $profile_id;

		/**
		 * @var	string	Start of date range
		 */
		protected $date_from;

		/**
		 * @var	string	End of date range
		 */
		protected $date_to;

		/**
		 * Creates new instance of vivvo_ga class
		 *
		 * @param	array	$params
		 */
		public function __construct(array $params = array()) {

			empty($params['email']) or $this->email = $params['email'];
			empty($params['password']) or $this->passwd = $params['password'];
			empty($params['profileId']) or $this->profile_id = $params['profileId'];

			if (!empty($this->email) and !empty($this->passwd) and empty($params['no_auth'])) {
				$this->authorize($this->email, $this->passwd);
			}

			$this->xml_parser = new VivvoXMLParser;
		}

		/**
		 * @var	array	Array of class instances
		 */
		private static $instances = array();

		/**
		 * Returns existing instance of vivvo_ga class or creates new
		 *
		 * @param	array	$params
		 * @return	vivvo_ga
		 */
		public static function get_instance(array $params = array()) {

			$key = md5(serialize($params));

			if (!isset(self::$instances[$key])) {
				self::$instances[$key] = new self($params);
			}

			return self::$instances[$key];
		}

		/**
		 * Returns new instance of vivvo_ga class
		 *
		 * @param	array	$params
		 * @return	vivvo_ga
		 */
		public static function factory(array $params = array()) {
			return new self(array_merge(
				array(
					'email' => VIVVO_GA_EMAIL,
					'password' => VIVVO_GA_PASSWORD
				),
				$params
			));
		}

		/**
		 * Sets Profile id
		 *
		 * @param	string	$profile_id
		 * @return	vivvo_ga
		 */
		public function setProfileId($profile_id) {

			if (is_numeric($profile_id)) {
				$this->profile_id = 'ga:' . $profile_id;
			} else {
				$this->profile_id = $profile_id;
			}

			return $this;
		}

		/**
		 * Sets date range
		 *
		 * @param	mixed	$from
		 * @param	mixed	$to
		 * @return	vivvo_ga
		 */
		public function setDateRange($from, $to) {

			if (!$from) {
				$this->date_from = date('Y-m-d', strtotime('-1 month'));
			} elseif (is_numeric($from)) {
				$this->date_from = date('Y-m-d', $from);
			} elseif (!preg_match('/\d{4}-\d{2}-\d{2}(\s+\d{2}:\d{2}:\d{2})?/', $from)) {
				$this->date_from = date('Y-m-d', strtotime($from));
			} else {
				list($this->date_from) = explode(' ', $from, 2);
			}

			if (!$to) {
				$this->date_to = date('Y-m-d');
			} elseif (is_numeric($to)) {
				$this->date_to = date('Y-m-d', $to);
			} elseif (!preg_match('/\d{4}-\d{2}-\d{2}(\s+\d{2}:\d{2}:\d{2})?/', $to)) {
				$this->date_to = date('Y-m-d', strtotime($to));
			} else {
				list($this->date_to) = explode(' ', $to, 2);
			}

			return $this;
		}

		/**
		 * Retrieves report from Google Analytics
		 *
		 * @param	array	$params
		 * @return	array
		 */
		public function getReport(array $params) {

			!empty($params['profileId']) or $params['profileId'] = $this->profile_id;
			!empty($params['date_from']) or $params['date_from'] = $this->date_from;
			!empty($params['date_to']) or $params['date_to'] = $this->date_to;

			$cache_key = 'ga_' . md5(serialize($params));

			if (vivvo_cache::get_instance()->exists($cache_key)) {
				return vivvo_cache::get_instance()->get($cache_key);
			}

			$this->setProfileId($params['profileId'])->setDateRange($params['date_from'], $params['date_to']);

			if (!empty($params['property']) and $this->profile_id) {

				$properties = array();

				foreach ($params['property'] as $key => $value) {
					$properties[] = urlencode($key) . '=' . urlencode($value);
				}

				if (count($properties)) {
					$properties = '&' . implode('&', $properties);
				} else {
					$properties = '';
				}

				try {

					$response = $this->call('https://www.google.com/analytics/feeds/data?ids=' . $this->profile_id . '&start-date=' . $this->date_from . '&end-date=' . $this->date_to . $properties);

					if ($response and $response->getStatus() == 200) {
						try {

							$xml = $this->xml_parser->parseString($response->getBody());

							$entries = array();

							foreach ($xml->rootNode->getElementsByNodeName('entry') as $node) {

								$entry = array(
									'dimensions' => array(),
									'metrics' => array()
								);

								foreach ($node->getElementsByNodeName('dxp:dimension') as $dimension) {
									$entry['dimensions'][$dimension->attributes['name']] = $dimension->attributes['value'];
								}

								foreach ($node->getElementsByNodeName('dxp:metric') as $metric) {
									$entry['metrics'][$metric->attributes['name']] = $metric->attributes['value'];
								}

								$entries[] = $entry;
							}

							vivvo_cache::get_instance()->put($cache_key, $entries, null, VIVVO_GA_CACHE_PERIOD);
							return $entries;

						} catch (VivvoXMLParserException $e) {
						}
					}
				} catch (HTTP_Request2_Exception $e) {
				}
			}

			return array();
		}

		/**
		 * Returns TopArticleViews report
		 *
		 * @param	int		$limit
		 * @param	array	$params
		 * @return	array
		 */
		public function getTopArticleViews($limit = 5, array $params = array()) {
			return $this->getReport(self::getParams('top_article_views', array_merge($params, array('limit' => $limit))));
		}

		/**
		 * Returns ArticleViews report
		 *
		 * @param	int		$article_id
		 * @param	array	$params
		 * @return	array
		 */
		public function getArticleViews($article_id, array $params = array()) {

			if (!empty($params['resolution']) && $params['resolution'] == 'hour') {
				$report = 'article_views_per_hour';
			} else {
				$report = 'article_views_per_day';
			}

			return $this->getReport(
				array_merge($params, self::getParams($report, array('article_id' => $article_id)))
			);
		}

		/**
		 * Returns ArticleStats report
		 *
		 * @param	mixed	$article_id
		 * @param	array	$params
		 * @return	array
		 */
		public function getArticleStats($article_id, array $params = array()) {

			if (!empty($params['resolution']) && $params['resolution'] == 'hour') {
				$report = 'article_stats_per_hour';
			} else {
				$report = 'article_stats_per_day';
			}

			return $this->getReport(
				array_merge($params, self::getParams($report, array('article_id' => $article_id)))
			);
		}

		/**
		 * Returns Events report
		 *
		 * @param	array	$params
		 * @return	array
		 */
		public function getEvents(array $params = array()) {
			return $this->getReport(array_merge($params, self::getParams('events')));
		}

		/**
		 * Returns Pageviews report
		 *
		 * @param	array	$params
		 * @return	array
		 */
		public function getPageviews(array $params = array()) {
			return $this->getReport(array_merge($params, self::getParams('pageviews')));
		}

		/**
		 * Returns Visitors report
		 *
		 * @param	array	$params
		 * @param	string	$period
		 * @return	array
		 */
		public function getVisitors(array $params = array(), $period = 'day') {
			return $this->getReport(array_merge($params, self::getParams('visitors_per_' . $period)));
		}

		/**
		 * Returns Browsers report
		 *
		 * @param	array	$params
		 * @return	array
		 */
		public function getBrowsers(array $params = array()) {
			return $this->getReport(array_merge($params, self::getParams('browsers')));
		}

		/**
		 * Returns OS report
		 *
		 * @param	array	$params
		 * @return	array
		 */
		public function getOperatingSystems(array $params = array()) {
			return $this->getReport(array_merge($params, self::getParams('operating_systems')));
		}

		/**
		 * Returns Screen-resolutions report
		 *
		 * @param	array	$params
		 * @return	array
		 */
		public function getScreenResolutions(array $params = array()) {
			return $this->getReport(array_merge($params, self::getParams('screen_resolutions')));
		}

		/**
		 * Returns Referrers report
		 *
		 * @param	array	$params
		 * @return	array
		 */
		public function getReferrers(array $params = array()) {
			return $this->getReport(array_merge($params, self::getParams('referrers')));
		}

		/**
		 * Returns Keywords report
		 *
		 * @param	array	$params
		 * @return	array
		 */
		public function getKeywords(array $params = array()) {
			return $this->getReport(array_merge($params, self::getParams('keywords')));
		}

		/**
		 * Returns query params for particular report
		 *
		 * @param	string	$report
		 * @param	array	$params
		 * @return	array
		 */
		public static function getParams($report = 'pageviews', array $params = array()) {

			switch ($report) {

				case 'top_article_views':
					$ret = array( 'property' => array(
						'dimensions' => 'ga:eventLabel',
						'metrics' => 'ga:totalEvents',
						'filters' => 'ga:eventCategory==Article;ga:eventAction==View;ga:eventLabel=~\d+',	// TODO: remove regex filter
						'sort' => '-ga:totalEvents',
						'max-results' => empty($params['limit']) ? 5 : $params['limit']
					));
					$ret['data_handler'] = 'article_views';
				break;

				case 'article_views':
				case 'article_views_per_day':
					$article_id = empty($params['article_id']) ? '0' : $params['article_id'];
					if (!is_array($article_id)) {
						$article_id = explode(',', $article_id);
					}
					$ret = array('property' => array(
						'dimensions' => 'ga:eventLabel,ga:day,ga:date',
						'metrics' => 'ga:totalEvents',
						'sort' => 'ga:day',
						'filters' => 'ga:eventCategory==Article;ga:eventAction==View;'
					));
					foreach ($article_id as $id) {
						$ret['property']['filters'] .= "ga:eventLabel==$id,";
					}
					$ret['property']['filters'] = rtrim($ret['property']['filters'], ',');
					$ret['data_handler'] = 'article_views';
				break;

				case 'article_overall_stats':
					$ret = array('property' => array(
						'dimensions' => 'ga:eventAction,ga:day,ga:date',
						'metrics' => 'ga:totalEvents',
						'sort' => 'ga:day',
						'filters' => 'ga:eventCategory==Article'
					));
					$ret['data_handler'] = 'article_overall_stats';
				break;

				case 'article_stats':
				case 'article_stats_per_day':
					$article_id = empty($params['article_id']) ? '0' : $params['article_id'];
					if (!is_array($article_id)) {
						$article_id = explode(',', $article_id);
					}
					$ret = array('property' => array(
						'dimensions' => 'ga:eventLabel,ga:eventAction,ga:day,ga:date',
						'metrics' => 'ga:totalEvents',
						'sort' => 'ga:day',
						'filters' => 'ga:eventCategory==Article;'
					));
					foreach ($article_id as $id) {
						$ret['property']['filters'] .= "ga:eventLabel==$id,";
					}
					$ret['property']['filters'] = rtrim($ret['property']['filters'], ',');
					$ret['data_handler'] = 'article_stats';
				break;

				case 'article_stats_per_hour':
					$article_id = empty($params['article_id']) ? '0' : $params['article_id'];
					if (!is_array($article_id)) {
						$article_id = explode(',', $article_id);
					}
					$ret = array('property' => array(
						'dimensions' => 'ga:eventLabel,ga:eventAction,ga:hour,ga:date',
						'metrics' => 'ga:totalEvents',
						'sort' => 'ga:hour',
						'filters' => "ga:eventCategory==Article;"
					));
					foreach ($article_id as $id) {
						$ret['property']['filters'] .= "ga:eventLabel==$id,";
					}
					$ret['property']['filters'] = rtrim($ret['property']['filters'], ',;');
					$ret['data_handler'] = 'article_stats';
				break;

				case 'article_views':
				case 'article_views_per_day':
					$article_id = empty($params['article_id']) ? '' : ';ga:eventLabel==' . $params['article_id'];
					$ret = array('property' => array(
						'dimensions' => 'ga:eventLabel,ga:day,ga:date',
						'metrics' => 'ga:totalEvents',
						'sort' => 'ga:date,ga',
						'filters' => "ga:eventCategory==Article;ga:eventAction==View$article_id"
					));
				break;

				case 'article_views_per_hour':
					$article_id = empty($params['article_id']) ? '0' : $params['article_id'];
					$ret = array('property' => array(
						'dimensions' => 'ga:eventLabel,ga:hour,ga:date',
						'metrics' => 'ga:totalEvents',
						'sort' => 'ga:date,ga:hour',
						'filters' => "ga:eventCategory==Article;ga:eventAction==View;ga:eventLabel==$article_id"
					));
				break;

				case 'events':
					$ret = array('property' => array(
						'dimensions' => 'ga:eventCategory,ga:eventAction,ga:eventLabel,ga:date',
						'metrics' => 'ga:totalEvents',
						'sort' => 'ga:date'
					));
				break;

				case 'pageviews':
					$ret = array('property' => array(
						'dimensions' => 'ga:date',
						'metrics' => 'ga:pageviews',
						'sort' => 'ga:date'
					));
				break;

				case 'visitors':
				case 'visitors_per_day':
					$ret = array('property' => array(
						'dimensions' => 'ga:date,ga:day',
						'metrics' => 'ga:visits',
						'sort' => 'ga:date'
					));
				break;

				case 'visitors_per_hour':
					$ret = array('property' => array(
						'dimensions' => 'ga:date,ga:hour',
						'metrics' => 'ga:visits',
						'sort' => 'ga:date,ga:hour'
					));
				break;

				case 'browsers':
					$ret = array('property' => array(
						'dimensions' => 'ga:browser,ga:browserVersion',
						'metrics' => 'ga:visits',
						'sort' => 'ga:visits'
					));
				break;

				case 'operating_systems':
					$ret = array('property' => array(
						'dimensions' => 'ga:operatingSystem',
						'metrics' => 'ga:visits',
						'sort' => 'ga:visits'
					));
				break;

				case 'screen_resolutions':
					$ret = array('property' => array(
						'dimensions' => 'ga:screenResolution',
						'metrics' => 'ga:visits',
						'sort' => 'ga:visits'
					));
				break;

				case 'referrers':
					$ret = array('property' => array(
						'dimensions' => 'ga:source',
						'metrics' => 'ga:visits',
						'sort' => 'ga:source'
					));
				break;

				case 'keywords':
					$ret = array('property' => array(
						'dimensions' => 'ga:keyword',
						'metrics' => 'ga:visits',
						'sort' => 'ga:keyword'
					));
				break;

				default:
					$ret = array('property' => array());

					foreach (array('dimensions', 'metrics', 'sort', 'filters', 'segment', 'start-index', 'max-results') as $key) {
						if (!empty($params[$key])) {
							$ret['property'][$key] = $params[$key];
						}
					}
			}

			foreach (array('date_from', 'date_to', 'profileId') as $key) {
				if (!empty($params[$key])) {
					$ret[$key] = $params[$key];
				}
			}

			return $ret;
		}

		/**
		 * Returns list of all available profiles
		 *
		 * @return	array
		 */
		public function getProfiles() {

			try {

				$response = $this->call('https://www.google.com/analytics/feeds/accounts/default');

				if ($response and $response->getStatus() == 200) {
					try {

						$xml = $this->xml_parser->parseString($response->getBody());

						$profiles = array();

						foreach ($xml->rootNode->getElementsByNodeName('entry') as $entry) {

							$profile = array();

							foreach ($entry->children as $child) {

								switch ($child->name) {

									case 'id':
									case 'updated':
									case 'title':
										$profile[$child->name] = $child->getTextContents(true);
									break;

									case 'dxp:property':
										if (!empty($child->attributes['name']) and !empty($child->attributes['value'])) {
											$profile[preg_replace('/^ga:/', '', $child->attributes['name'])] = $child->attributes['value'];
										}
									break;

									case 'dxp:tableId':
										$profile['tableId'] = $child->getTextContents(true);
									break;
								}
							}

							$profiles[] = $profile;
						}

						return $profiles;

					} catch (VivvoXMLParserException $e) {
					}
				}
			} catch (HTTP_Request2_Exception $e) {
			}

			return array();
		}

		/**
		 * Requests authorization code from GA
		 *
		 * @param	string	$email
		 * @param	string	$password
		 * @param	bool	$force
		 * @return	bool
		 */
		public function authorize($email = '', $password = '', $force = false) {

			if (!$force and !empty($this->auth_code) and $email == $this->email and $password == $this->passwd) {
				return true;
			}

			unset($this->auth_code);

			!empty($email) or $email = $this->email;
			!empty($password) or $password = $this->passwd;

			if (empty($email) or empty($password)) {
				return false;
			}

			try {

				$response = $this->post(
					'https://www.google.com/accounts/ClientLogin',
					array(
						'accountType' => 'GOOGLE',
						'Email' => $this->email = $email,
						'Passwd' => $this->passwd = $password,
						'service' => 'analytics',
						'source' => 'Spoonlabs-VivvoCMS-' . VIVVO_VERSION
					)
				);

				if ($response and $response->getStatus() == 200 and preg_match('/(?:^|[\n\r])Auth=(.*?)(?:[\n\r]|$)/', $response->getBody(), $match)) {

					$this->auth_code = $match[1];

					return true;
				}

			} catch (HTTP_Request2_Exception $e) {
			}

			return false;
		}

		/**
		 * 	Calls Data Export API method, ensures that request have valid authorization header
		 *
		 *	@param	string	$url
		 *	@param	array	$params
		 *	@param	array	$headers
		 *	@return	HTTP_Request2_Response|false
		 */
		protected function call($url, array $params = array(), array $headers = array()) {

			if (!$this->auth_code && !$this->authorize($this->email, $this->passwd, true)) {
				return false;
			}

			$headers['Authorization'] = 'GoogleLogin auth=' . $this->auth_code;

			if (empty($params)) {
				return $this->get($url, $headers);
			} else {
				return $this->post($url, $params, $headers);
			}
		}

		/**
		 * Sends POST request to specified url
		 *
		 * @param	string	$url
		 * @param	array	$params
		 * @param	array	$headers
		 * @return	HTTP_Request2_Response
		 */
		protected function post($url, array $params = array(), array $headers = array()) {
			return $this->send(HTTP_Request2::METHOD_POST, $url, $params, $headers);
		}

		/**
		 * Sends GET request to specified url
		 *
		 * @param	string	$url
		 * @param	array	$headers
		 * @return	HTTP_Request2_Response
		 */
		protected function get($url, array $headers = array()) {
			return $this->send(HTTP_Request2::METHOD_GET, $url, array(), $headers);
		}

		/**
		 * The actual method for sending requests
		 *
		 * @param	string	$url
		 * @param	array	$headers
		 * @param	array	$params
		 * @return	HTTP_Request2_Response
		 */
		protected function send($method, $url, array $params = array(), array $headers = array()) {

			$headers['GData-Version'] = '2';
			$headers['User-Agent'] = 'Vivvo/' . VIVVO_VERSION . ' (' . self::$request_adapter . ') PHP/' . PHP_VERSION;

			$request = new HTTP_Request2($url);
			$request->setAdapter(self::$request_adapter);
			$request->setConfig('ssl_verify_peer', false);
			$request->setConfig('follow_redirects', true);
			$request->setHeader($headers);
			$request->setMethod($method);

			if ($method == HTTP_Request2::METHOD_POST) {
				$request->addPostParameter($params);
			}

			return $request->send();
		}
	}

	// use curl adapter if available
	extension_loaded('curl') and vivvo_ga::$request_adapter = 'curl';

	/**
	 * Google Analytics chart data provider class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class vivvo_ga_chart_provider extends vivvo_chart_data_provider {

		/**
		 * Returns chart settings
		 *
		 * @param 	array		$params
		 * @return	array
		 */
		public function get_chart_settings(array $params = array()) {
			$sm = vivvo_lite_site::get_instance();
			if (!$sm->user) {
				return array();
			}

			$cache_key = 'ga_chart_settings_' . md5(serialize($params));

			if (vivvo_cache::get_instance()->exists($cache_key)) {
				return vivvo_cache::get_instance()->get($cache_key);
			}

			$ga = new vivvo_ga(array(
				'email' => VIVVO_GA_EMAIL,
				'password' => VIVVO_GA_PASSWORD,
				'profileId' => VIVVO_GA_PROFILEID,
				'no_auth' => true
			));

			$ret = array(
				'colors' => '#0D8ECF,#B0DE09,#F2B31C,#65CDAA,#CBB9A3,#EC6AA6,#874A85,#8E0001,#2C862E,#99CCFF',
				'scroller' => array(
					'height' => '15',
				),
				'grid' => array(
					'x' => array(
						'approx_count' => '8',
					),
				),
				'indicator' => array(
					'color' => '#0D8ECF',
					'line_alpha' => '50',
					'selection_color' => '#0D8ECF',
					'x_balloon_text_color' => '#FFFFFF',
				),
				'legend' => array(
					'text_color_hover' => '#FF2D1F',
				),
			);

			$report = $ga->getReport($params);
			$ret['graphs'] = array('graph[]' => array());
			$graph_id = 0;

			empty($params['data_handler']) and $params['data_handler'] = '';

			switch ($params['data_handler']) {

				case 'article_views':
					$labels = array();

					foreach ($report as $row) {
						$labels[$row['dimensions']['ga:eventLabel']] = 1;
					}

					class_exists('Articles') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php';

					$articles = Articles_list::factory()->get_articles_by_ids(array_keys($labels));

					foreach ($articles as $article) {
						$ret['graphs']['graph[]'][] = array(
							'@gid' => ++$graph_id,
							'title' => $article->get_title(),
							'color_hover' => '#FF2D1F',
							'line_width' => '2'
						);
					}
				break;

				case 'article_overall_stats':
				case 'article_stats':
					$labels = array();

					foreach ($report as $row) {
						$labels[$row['dimensions']['ga:eventAction']] = 1;
					}

					foreach (array_keys($labels) as $label) {
						$ret['graphs']['graph[]'][] = array(
							'@gid' => ++$graph_id,
							'title' => $label,
							'color_hover' => '#FF2D1F',
							'line_width' => '2'
						);
					}
				break;

				default:
					$report = reset($report);

					foreach ($report['metrics'] as $metric => $value) {
						$ret['graphs']['graph[]'][] = array(
							'@gid' => ++$graph_id,
							'title' => preg_replace('/^ga:/', '', $metric),
							'color_hover' => '#FF2D1F',
							'line_width' => '2'
						);
					}
			}


			vivvo_cache::get_instance()->put($cache_key, $ret, null, VIVVO_GA_CACHE_PERIOD);
			return $ret;
		}

		/**
		 * Returns chart data
		 *
		 * @param 	array		$params
		 * @return	array
		 */
		public function get_chart_data(array $params = array()) {

			$sm = vivvo_lite_site::get_instance();

			if (!$sm->user) {
				return array();
			}

			$cache_key = 'ga_chart_data_' . md5(serialize($params));

			if (vivvo_cache::get_instance()->exists($cache_key)) {
				return vivvo_cache::get_instance()->get($cache_key);
			}

			$ga = vivvo_ga::get_instance(array(
				'email' => VIVVO_GA_EMAIL,
				'password' => VIVVO_GA_PASSWORD,
				'profileId' => VIVVO_GA_PROFILEID,
				'no_auth' => true
			));

			$report = $ga->getReport($params);

			if (empty($report)) {
				return array(0);
			}

			$data = array();

			empty($params['data_handler']) and $params['data_handler'] = '';

			$min = $max = 0;

			switch ($params['data_handler']) {

				case 'article_views':
					$labels = array();

					foreach ($report as $row) {
						$labels[$row['dimensions']['ga:eventLabel']] = 1;
					}

					$labels = array_keys($labels);

					foreach ($report as $result) {
						if (isset($result['dimensions']['ga:date']) && preg_match('/^(\d{4})(\d{2})(\d{2})$/', $result['dimensions']['ga:date'], $part)) {

							if (isset($result['dimensions']['ga:hour'])) {
								$hour = $result['dimensions']['ga:hour'] << 0;
								$format = 'd M Y H\h';
								$step = 3600;
							} else {
								$hour = 0;
								$format = 'd M Y';
								$step = 86400;
							}

							$time = mktime($hour, 0, 0, $part[2], $part[3], $part[1]);

							$time > $max and $max = $time;
							if ($time < $min or $min == 0) {
								$min = $time;
							}

							$key = format_date(date('Y-m-d H:00:00', $time), $format);

							if (!isset($data[$key])) {
								$data[$key] = array_fill(0, count($labels), 0);
							}

							$data[$key][array_search($result['dimensions']['ga:eventLabel'], $labels)] = $result['metrics']['ga:totalEvents'];
						}
					}
				break;

				case 'article_overall_stats':
				case 'article_stats':
					$labels = array();

					foreach ($report as $row) {
						$labels[$row['dimensions']['ga:eventAction']] = 1;
					}

					$labels = array_keys($labels);

					foreach ($report as $result) {
						if (isset($result['dimensions']['ga:date']) && preg_match('/^(\d{4})(\d{2})(\d{2})$/', $result['dimensions']['ga:date'], $part)) {

							if (isset($result['dimensions']['ga:hour'])) {
								$hour = $result['dimensions']['ga:hour'] << 0;
								$format = 'd M Y H\h';
								$step = 3600;
							} else {
								$hour = 0;
								$format = 'd M Y';
								$step = 86400;
							}

							$time = mktime($hour, 0, 0, $part[2], $part[3], $part[1]);

							$time > $max and $max = $time;
							if ($time < $min or $min == 0) {
								$min = $time;
							}

							$key = format_date(date('Y-m-d H:00:00', $time), $format);

							if (!isset($data[$key])) {
								$data[$key] = array_fill(0, count($labels), 0);
							}

							$data[$key][array_search($result['dimensions']['ga:eventAction'], $labels)] = $result['metrics']['ga:totalEvents'];
						}
					}
				break;

				default:
					foreach ($report as $result) {
						if (isset($result['dimensions']['ga:date']) && preg_match('/^(\d{4})(\d{2})(\d{2})$/', $result['dimensions']['ga:date'], $part)) {

							if (isset($result['dimensions']['ga:hour'])) {
								$hour = $result['dimensions']['ga:hour'] << 0;
								$format = 'd M Y H\h';
								$step = 3600;
							} else {
								$hour = 0;
								$format = 'd M Y';
								$step = 86400;
							}

							$time = mktime($hour, 0, 0, $part[2], $part[3], $part[1]);

							$time > $max and $max = $time;
							if ($time < $min or $min == 0) {
								$min = $time;
							}

							$key = format_date(date('Y-m-d H:00:00', $time), $format);

							$data[$key] = array();
							foreach ($result['metrics'] as $metric => $value) {
								$data[$key][] = $value;
							}
						}
					}
			}

			$sample = reset($data);

			if (is_array($sample)) {
				$empty = array_fill(0, count($sample), 0);
			} else {
				$empty = 0;
			}

			for ($i = $min; $i < $max; $i += $step) {

				$key = format_date(date('Y-m-d H:00:00', $i), $format);

				if (!isset($data[$key])) {
					$data[$key] = $empty;
				}
			}

			uksort($data, array(self, 'date_string_compare'));

			$ret = array();
			foreach ($data as $date => $values) {
				array_unshift($values, $date);
				$ret[] = $values;
			}

			vivvo_cache::get_instance()->put($cache_key, $ret, null, VIVVO_GA_CACHE_PERIOD);

			return $ret;
		}

		/**
		 * Compares two date strings
		 *
		 * @param	string	$a
		 * @param	string	$b
		 * @return	int
		 */
		private static function date_string_compare($a, $b) {
			return strtotime($a) - strtotime($b);
		}

		/**
		 * Filters params passed trough box_chart
		 *
		 * @param	array	$params		Array of data selection parameters
		 * @return	array
		 */
		public function filter_box_params(array $params = array()) {

			if (empty($params['date_from'])) {
				$params['date_from'] = date('Y-m-d H:00:00', strtotime('-1 month'));
			} elseif (is_numeric($params['date_from'])) {
				$params['date_from'] = date('Y-m-d H:00:00', $params['date_from']);
			} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}(\s\d{2}:\d{2}:\d{2})?$/', $params['date_from'])) {
				$params['date_from'] = date('Y-m-d H:00:00', strtotime($params['date_from']));
			}

			if (empty($params['date_to'])) {
				$params['date_to'] = date('Y-m-d H:00:00');
			} elseif (is_numeric($params['date_to'])) {
				$params['date_to'] = date('Y-m-d H:00:00', $params['date_to']);
			} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}(\s\d{2}:\d{2}:\d{2})?$/', $params['date_to'])) {
				$params['date_to'] = date('Y-m-d H:00:00', strtotime($params['date_to']));
			}

			if (empty($params['report'])) {
				$params['report'] = 'pageviews';
			}

			$params = array_merge($params, vivvo_ga::getParams($params['report'], $params));

			foreach (array('report', 'dimensions', 'metrics', 'sort', 'filters', 'segment', 'start-index', 'max-results') as $key) {
				unset($params[$key]);
			}

			return $params;
		}
	}


	/**
	 * Analytics box module class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class box_analytics extends module {

		public function generate_output($params = array()) {

			static $data_provider = null;
			static $ga = null;

			$data_provider != null or $data_provider = new vivvo_ga_chart_provider;

			$ga != null or $ga = vivvo_ga::get_instance(array(
				'email' => VIVVO_GA_EMAIL,
				'password' => VIVVO_GA_PASSWORD,
				'profileId' => VIVVO_GA_PROFILEID,
				'no_auth' => true
			));

			$this->set_template($params);

			unset($params['template_string']);
			unset($params['template']);

			$params = $data_provider->filter_box_params($params);

			$cache_key = 'ga_box_data_' . md5(serialize($params));

			if (vivvo_cache::get_instance()->exists($cache_key)) {

				$report = vivvo_cache::get_instance()->get($cache_key);

			} else {

				$report = $ga->getReport($params);

				empty($params['data_handler']) and $params['data_handler'] = '';

				switch ($params['data_handler']) {

					case 'article_views':
						class_exists('Articles') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php';

						$article_ids = array();

						foreach ($report as $row) {
							$article_ids[$row['dimensions']['ga:eventLabel']] = 1;
						}

						$articles = Articles_list::factory()->get_articles_by_ids(array_keys($article_ids));

						for ($i = 0, $count = count($report); $i < $count; $i++) {
							if (!empty($articles[$report[$i]['dimensions']['ga:eventLabel']])) {
								$report[$i]['article'] = $articles[$report[$i]['dimensions']['ga:eventLabel']];
								$report[$i]['views'] = $report[$i]['metrics']['ga:totalEvents'];
							} else {
								unset($report[$i]);
							}
						}

						$report = array_merge($report);

					default:
				}

				vivvo_cache::get_instance()->put($cache_key, $report, null, VIVVO_GA_CACHE_PERIOD);
			}

			$this->_template->assign('report', $report);
		}
	}

#EOF