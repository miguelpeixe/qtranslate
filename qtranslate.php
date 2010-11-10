<?php

// encoding: utf-8
/*
  Plugin Name: qTranslate
  Plugin URI: http://www.qianqin.de/qtranslate/
  Description: Adds userfriendly multilingual content support into Wordpress. For Problems visit the <a href="http://www.qianqin.de/qtranslate/forum/">Support Forum</a>.
  Version: 3.0a
  Author: Qian Qin
  Author URI: http://www.qianqin.de
  Text Domain: qtranslate
  Domain Path: /lang
 */
/*
  Flags in flags directory are made by Luc Balemans and downloaded from
  FOTW Flags Of The World website at http://flagspot.net/flags/
  (http://www.crwflags.com/FOTW/FLAGS/wflags.html)
 */
/*
  Copyright 2010  Qian Qin  (email : mail@qianqin.de)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
/*
  Default Language Contributers
  =============================
  en, de by Qian Qin
  zh by Junyan Chen
  fi by Tatu Siltanen
  fr by Damien Choizit
  nl by RobV
  se by bear3556, johdah
  it by Lorenzo De Tomasi
  ro, hu by Jani Monoses
  ja by Brian Parker
  es by June
  vi by hathhai
  ar by Mohamed Magdy

  Plugin Translation Contributers
  ===============================
  en, de by Qian Qin
  es by Alejandro Urrutia
  fr by eriath
  tr by freeuser
  it by shecky
  nl by BlackDex
  id by Masino Sinaga
  pt by claudiotereso
  az by evlenirikbiz
  bg by Dimitar Mitev
  da by meviper
  mk by Pavle Boskoski

  Sponsored Features
  ==================
  Excerpt Translation by bastiaan van rooden (www.nothing.ch)

  Specials thanks
  ===============
  Christoph (Gift), cuba design(Donation), Jevgenijs (Donation), Eike (Donation), nothing GmbH (Donation), UltraSnow.de (Donation), Maximilian (Donation), Sparktivity LLC (Donation), Hotchkiss Consulting, LLC (Donation), Thomas (Donation), Julie (Donation), Sami (Donation), Arming (Gift), Riccardo (Donation), Tim (Gift), Bernhard (Donation), Benjamin (Donation), Dominique (Donation), Robert (Donation), Andrea (Donation), Cynllunio Pentir Design (Donation), Johannes (Donation), Pierre (Donation), Data Test Lab bvba (Donation), Rural China Education Fundation (Donation), Dimitri (Donation), Tammo (Donation), Benjamin (Donation), Jacques (Donation), Robert (Donation), Alexis (Gift), Roger (Donation), Carmen (Gift), Jean-Pierre (Postcard), Bruno (Gift), Andrea (Gift), Daniele (Postcard), Gerlando (Donation), Bostjan (Donation)
 */
/**
 * qTranslate Plugin File
 *
 * contains most of the classes and functions for internal usage.
 *
 * @author Qian Qin
 * @package qTranslate
 *
 */
define('QT_MODE_PATH', 0);
define('QT_MODE_DOMAIN', 1);

/**
 * Language Class
 *
 * specifies a language
 *
 * @package qTranslate
 */
class Language {

	/**
	 * {@link http://en.wikipedia.org/wiki/ISO_639 ISO 639} code
	 * (example: en)
	 */
	var $code;
	/**
	 * Name
	 * (example: English)
	 */
	var $name;
	/**
	 * Locale
	 * (example: en_US)
	 */
	var $locale;
	/**
	 * Windows Locale (usually full language name)
	 * (example: English)
	 */
	var $windowsLocale;
	/**
	 * Not Available Message
	 * %LANG:<normal_seperator>:<last_seperator>% generates a list of languages
	 * seperated by <normal_seperator> except for the last one, where <last_seperator>
	 * will be used instead.
	 * (example: Sorry, this entry is only available in %LANG:, : and %.)
	 */
	var $naMessage;
	/**
	 * Date format using either {@link http://www.php.net/manual/en/function.date.php date}
	 * or {@link http://www.php.net/manual/en/function.strftime.php strftime}
	 * (example: F jS, Y)
	 */
	var $dateFormat;
	/**
	 * Time format using either {@link http://www.php.net/manual/en/function.date.php date}
	 * or {@link http://www.php.net/manual/en/function.strftime.php strftime}
	 * (example: g:i a)
	 */
	var $timeFormat;
	/**
	 * Flag file name
	 * (example: gb.png)
	 */
	var $flag;
	/**
	 * Text direction, either "ltr" or "rtl"
	 */
	var $direction;
	/**
	 * Domain (only used in domain mode)
	 */
	var $domain;

	/**
	 * Constructor for language class
	 *
	 * @param string $code
	 * @param string $name
	 * @param string $locale
	 * @param string $naMessage
	 * @param string $dateFormat
	 * @param string $timeFormat
	 * @param string $flag
	 * @param string $domain [optional]
	 */
	function __construct($code, $name, $locale, $naMessage, $dateFormat, $timeFormat, $flag, $direction = 'ltr', $domain = '', $windowsLocale = '') {
		if ($direction != 'rtl')
			$direction = 'ltr';
		$this->code = $code;
		$this->name = $name;
		$this->locale = $locale;
		$this->naMessage = $naMessage;
		$this->dateFormat = $dateFormat;
		$this->timeFormat = $timeFormat;
		$this->flag = $flag;
		$this->direction = $direction;
		$this->domain = $domain;
		$this->windowsLocale = empty($windowsLocale) ? $name : $windowsLocale;
	}

	/**
	 *
	 * @return string  returns the language name and locale
	 */
	function __toString() {
		return $this->name . ' (' . $this->locale . ')';
	}

}

/**
 * qTranslate Class
 *
 * contains the core functions of qTranslate
 *
 * @package qTranslate
 *
 */
class qTranslate {

	/**
	 * Stores the current display language
	 */
	var $language;
	/**
	 * Stores the default display language
	 */
	var $defaultLanguage;
	/**
	 * Stores all language informations
	 */
	var $languages = array();
	/**
	 * Stores the current URL mode
	 */
	var $mode;
	/**
	 * Stores currently enabled languages
	 */
	var $enabledLanguages = array();

	/**
	 * Initializes qTranslate
	 */
	function init() {
		// Tell wordpress that there is now a language parameter
		$this->loadSettings();
		$this->detectLanguage();
		$this->setLocale();
		if ($this->mode == QT_MODE_PATH)
			$this->setupPathMode();
		if (defined('WP_ADMIN')) {
			// Admin only filters
			add_filter('core_version_check_locale', array('qTranslate', 'versionLocaleFilter'));
		} else {
			// Non admin filters
			add_filter('request', array($this, 'fixRequestFilter'), 0, 1);
			add_filter('option_date_format', array($this, 'currentDateFormat'));
			add_filter('option_time_format', array($this, 'currentTimeFormat'));
			add_action('init', array($this, 'setupPostTypeAction'));
		}
		add_filter('date_i18n', array($this, 'formatTimeFilter'), 0, 4);
		do_action_ref_array('qtranslate_init', array(&$this));
	}

	/**
	 * Let Wordpress Auto-Updater check for updates for original Wordpress instance
	 * @static
	 * @return string  always returns en_US
	 */
	function versionLocaleFilter() {
		return 'en_US';
	}

	/**
	 * Add custom post type for translations
	 */
	function setupPostTypeAction() {
		foreach ($this->enabledLanguages as $language) {
			$args = array(
				'public' => false,
				'publicly_queryable' => false,
				'exclude_from_search' => false,
				'show_ui' => false,
				'hierarchical' => false,
				'query_var' => true,
				'rewrite' => true,
				'capability_type' => 'post',
				'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions')
			);

			register_post_type('translation-' . $language, $args);
		}
	}

	/**
	 * Do things needed to get Path-Mode running
	 */
	function setupPathMode() {
		add_rewrite_tag('%lang%', '[A-Za-z]{2,3}');
		add_filter('option_rewrite_rules', array($this, 'updateRewriteRulesFilter'), 10, 1);
	}

	/**
	 * Do things needed to start up Domain-Mode
	 */
	function setupDomainMode() {
		//TODO: Domain Mode
	}

	/**
	 * Add language tag to rewrite rules
	 *
	 * @param array $old current rules
	 * @return array  new rules merged with old rules
	 */
	function updateRewriteRulesFilter($old) {
		$new = array();
		if (qTranslate::startsWith($key, 'index.php/')) {
			$new['index.php/[A-Za-z]{2,3}/?$'] = 'index.php';
			foreach ($old as $key => $rule) {
				$new['index.php/[A-Za-z]{2,3}/' . substr($key, 10)] = $rule;
			}
		} else {
			$new['[A-Za-z]{2,3}/?$'] = 'index.php';
			foreach ($old as $key => $rule) {
				$new['[A-Za-z]{2,3}/' . $key] = $rule;
			}
		}
		return array_merge($new, $old);
	}

	/**
	 * Checks whether a language is enabled or not
	 *
	 * @param string $lang language code to test
	 * @return bool  true if language is enabled
	 */
	function isEnabled($lang) {
		return in_array($lang, $this->enabledLanguages);
	}

	/**
	 * Compares two string so see if $s starts with $n
	 *
	 * @static
	 * @param string $s string to check in
	 * @param string $n string to look for
	 * @return bool  true if $s starts with $n
	 */
	function startsWith($s, $n) {
		if (strlen($n) > strlen($s))
			return false;
		if ($n == substr($s, 0, strlen($n)))
			return true;
		return false;
	}

	/**
	 * Loads up all settings
	 */
	function loadSettings() {
		$this->languages = get_option('qtranslate_languages', array(
					'en' => new Language(
							'en',
							'English',
							'en_US',
							'Sorry, this entry is only available in %LANG:, : and %.',
							'F jS, Y',
							'g:i a',
							'gb.png'
					),
					'de' => new Language(
							'de',
							'Deutsch',
							'de_DE',
							'Leider ist der Eintrag nur auf %LANG:, : und % verfügbar.',
							'%A, der %e. %B %Y',
							'%H:%M',
							'de.png'
					)
				));
		$this->enabledLanguages = get_option('qtranslate_enabled_languages', array('en', 'de'));
		$this->defaultLanguage = get_option('qtranslate_default_langauge', 'en');
		$this->mode = get_option('qtranslate_mode', QT_MODE_PATH);
	}

	/**
	 * Detects the current language and saves it in $language
	 */
	function detectLanguage() {
		global $wp_rewrite;
		$this->language = $this->defaultLanguage;
		$language = '';
		$permalink = get_option('permalink_structure');
		if (isset($_GET['lang']) && $this->isEnabled($_GET['lang'])) {
			$language = $_GET['lang'];
		} elseif ($this->mode == QT_MODE_DOMAIN) {
			// TODO: Domain mode
		} elseif ($this->mode == QT_MODE_PATH && $permalink
				!= '') {
			$req_uri = $_SERVER['REQUEST_URI'];
			$home_path = parse_url(home_url());
			if (isset($home_path['path']))
				$home_path = $home_path['path'];
			else
				$home_path = '';
			$home_path = trim($home_path, '/');
			// remove home path and pathinfo
			$req_uri = trim($req_uri, '/');
			$req_uri = preg_replace("|^$home_path|", '', $req_uri);
			$req_uri = trim($req_uri, '/');
			if (qTranslate::startsWith($permalink, '/index.php/'))
				$req_uri = preg_replace("|^index\.php/|", '', $req_uri);
			if (preg_match('#^([A-Za-z]{2,3})/#', $req_uri, $match)) {
				$language = $match[1];
			} elseif (preg_match('#^([A-Za-z]{2,3})$#', $req_uri, $match)) {
				$language = $match[1];
			}
			// let WordPress know it's the index page
			if (preg_match('#^([A-Za-z]{2,3})/?$#', $req_uri)) {
				$wp_rewrite->index = $req_uri;
			}
		}
		$language = apply_filters('qtranslate_detected_language', $language);
		if ($this->isEnabled($language))
			$this->language = $language;
	}

	/**
	 * Set the correct locale for the current language
	 */
	function setLocale() {
		global $locale, $wp_locale;
		$locale = $this->languages[$this->language]->locale;
		$wp_locale->text_direction = $this->languages[$this->language]->direction;
		// set locale so strftime works correctly
		$localelist = array(
			$this->languages[$this->language]->locale . '.utf8',
			$this->languages[$this->language]->locale . '@euro',
			$this->languages[$this->language]->locale,
			substr($this->language, 0, 2),
			$this->languages[$this->language]->windowsLocale
		);
		setlocale(LC_TIME, $localelist);
	}

	/**
	 * Changes post_type to translation, so WordPress fetches the translations instead of the real article
	 *
	 * @param array $query current query variables
	 * @return array $query converted query variables
	 */
	function fixRequestFilter($query) {
		$supportedTypes = apply_filters('qtranslate_supported_types', array('post', 'page'));
		if (!is_array($query['post_type'])) {
			if (empty($query['post_type']) || in_array($query['post_type'], $supportedTypes)) {
				$query['post_type'] = 'translation-' . $this->language;
			}
		}
		return $query;
	}

	/**
	 * Checks if string has language tag or quick tag
	 * @static
	 * @param string $text text to check
	 * @return bool  true if tag has been detected
	 */
	function hasTag($text) {
		if (preg_match('#\[:[a-z]{2,3}\]#i', $text))
			return true;
		if (preg_match('#<!--:[a-z]{2,3}-->#i', $text))
			return true;
		return false;
	}

	/**
	 * Detects strftime formats by looking for % without prepending \
	 * @static
	 * @param string $format
	 * @return bool  true if format is a strftime format
	 */
	function isStrftimeFormat($format) {
		return preg_match('#[^\\\\]%#', $format);
	}

	/**
	 * Returns the current date format
	 * @return string  current date format
	 */
	function currentDateFormat() {
		return $this->languages[$this->language]->dateFormat;
	}

	/**
	 * Returns the current time format
	 * @return string  current time format
	 */
	function currentTimeFormat() {
		return $this->languages[$this->language]->timeFormat;
	}

	/**
	 * Format the datetime according to current language with current format
	 * @param string $old_date
	 * @param string $format
	 * @param string $time
	 * @param bool $gmt
	 * @return string
	 */
	function formatTimeFilter($old_date, $format, $time, $gmt) {
		$hasTag = qTranslate::hasTag($format);
		$isStrftime = qTranslate::isStrftimeFormat($format);
		if (!$hasTag && !$isStrftime && !empty($old_date))
			return $old_date;
		if ($hasTag)
			$format = $this->translate($format);
		if ($isStrftime) {
			// strftime given, so use it
			return strftime($format, $time);
		} else {
			// use wordpress internal date
			return date_i18n($format, $time, $gmt);
		}
	}

	/**
	 * Filters text to only show the current language
	 * @param mixed $text text to filter
	 * @param mixed $language optional language to filter, defaults to current language
	 * @return mixed  filtered text
	 */
	function translate($text, $language = false) {
		if ($language === false || !$this->isEnabled($language))
			$language = $this->language;
		// convert string directly, arrays and objects recursively
		if (is_string($text)) {
			$content = $this->split($text);
			if (isset($content[$language])) {
				$text = $content[$language];
			} else {
				$text = '';
			}
		} elseif (is_array($text)) {
			foreach ($text as $key => $val) {
				$text[$key] = $this->translate($text[$key], $language);
			}
		} elseif (is_object($text) || @get_class($text) == '__PHP_Incomplete_Class') {
			foreach (get_object_vars($text) as $key => $val) {
				$text->$key = $this->translate($text[$key], $language);
			}
		}
		return $text;
	}

	/**
	 * Splits text so all language can be accessed seperately via array
	 * @param string $text text to split
	 * @param bool $quicktags split at quicktags
	 * @return array $content array with text for all detected languages
	 */
	function split($text, $quicktags = true) {
		//init vars
		$split_regex = "#(<!--[^-]*-->|\[:[a-z]{2,3}\])#ism";
		$current_language = "";
		$result = array();
		foreach ($this->enabledLanguages as $language) {
			$result[$language] = "";
		}

		// split text at all xml comments
		$blocks = preg_split($split_regex, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach ($blocks as $block) {
			# detect language tags
			if (preg_match("#^<!--:([a-z]{2,3})-->$#ism", $block, $matches)) {
				if ($this->isEnabled($matches[1])) {
					$current_language = $matches[1];
				} else {
					$current_language = "invalid";
				}
				continue;
				// detect quicktags
			} elseif ($quicktags && preg_match("#^\[:([a-z]{2,3})\]$#ism", $block, $matches)) {
				if ($this->isEnabled($matches[1])) {
					$current_language = $matches[1];
				} else {
					$current_language = "invalid";
				}
				continue;
				// detect ending tags
			} elseif (preg_match("#^<!--:-->$#ism", $block, $matches)) {
				$current_language = "";
				continue;
				// detect defective more tag
			} elseif (preg_match("#^<!--more-->$#ism", $block, $matches)) {
				foreach ($this->enabledLanguages as $language) {
					$result[$language] .= $block;
				}
				continue;
			}
			// correctly categorize text block
			if ($current_language == "") {
				// general block, add to all languages
				foreach ($this->enabledLanguages as $language) {
					$result[$language] .= $block;
				}
			} elseif ($current_language != "invalid") {
				// specific block, only add to active language
				$result[$current_language] .= $block;
			}
		}
		foreach ($result as $lang => $lang_content) {
			$result[$lang] = preg_replace("#(<!--more-->|<!--nextpage-->)+$#ism", "", $lang_content);
		}
		return $result;
	}

}

// Initialize qTranslate on action "setup_theme" with priority 0, so it gets loaded before almost everything else.
// If something should run before qTranslate, it should hook into "plugins_loaded".
if (defined('ABSPATH') && defined('WPINC')) {
	global $qt;
	$qt = new qTranslate();
	add_action('setup_theme', array($qt, 'init'), 0, 0);
}
?>