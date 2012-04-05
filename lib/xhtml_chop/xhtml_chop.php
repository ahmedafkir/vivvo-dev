<?php

/**
 * XHTML Chop class by Aleksandar Ruzicic
 *
 * Public domain, use and abuse as you like
 */

	define('XHTML_CHOP_WORDS',            	0x000);
	define('XHTML_CHOP_CHARS',            	0x001);
	define('XHTML_CHOP_NO_WORD_BREAK',    	0x010);
	define('XHTML_CHOP_ELIPSIS_OUT',		0x020);
	define('XHTML_CHOP_NO_EMPTY_TAGS',    	0x040);
	define('XHTML_CHOP_UTF8',				0x100);
	define('XHTML_CHOP_LETTERS',        	XHTML_CHOP_CHARS | XHTML_CHOP_NO_WORD_BREAK);

	class XHTMLChop {

		/**
		 * Chops given xhtml string to given number of words/charaters
		 *
		 * @param	string	$html
		 * @param	int		$max
		 * @param	int		$mode
		 * @param	string	$elipsis
		 * @return	string
		 */
		public static function chop($html, $max, $mode = XHTML_CHOP_WORDS, $elipsis = '...') {

			$stack = array();
			$ret = '';
			$total = 0;
			$done = false;

			$nonempty = create_function('$word', 'return !empty($word);');

			$u = ($multibyte = $mode & XHTML_CHOP_UTF8 && function_exists('mb_internal_encoding')) ? 'u' : '';

			if ($multibyte) {

				$old_encoding = mb_internal_encoding();
				mb_internal_encoding('UTF-8');

				$strlen = 'mb_strlen';
				$substr = 'mb_substr';
				$strrpos = 'mb_strrpos';
				$strtolower = 'mb_strtolower';
			} else {
				$strlen = 'strlen';
				$substr = 'substr';
				$strtolower = 'strtolower';
				$strrpos = 'strrpos';
			}

			$html = strtr(
				preg_replace('/\s+/'.$u, ' ', $html),
				array(
					'&zwnj;' => self::unichr(8204),
					'&zwj;'  => self::unichr(8205),
					'&lrm;'  => self::unichr(8206),
					'&rlm;'  => self::unichr(8207)
			));

			while ($html and !$done) {

				if (!preg_match('|<(/?\w+)[^>]*/?>|'.$u, $html, $matches, PREG_OFFSET_CAPTURE)) {

					$text = $html;
					$tag = false;

				} else {

					$tag = $matches[0][0];
					$tag_start = $matches[0][1];
					$text = $substr($html, 0, $tag_start);
				}

				if ($text) {

					if ($text[0] == ' ') {
						$ret .= ' ';
					}

					if ($mode & XHTML_CHOP_CHARS) {

						$num_chars = $strlen($text = trim($text, ' '));

						if ($total + $num_chars > $max) {

							$done = true;

							if ($mode & XHTML_CHOP_NO_WORD_BREAK) {
								$num_chars = $max - $total;
								$text = $substr($text, 0, $num_chars) . preg_replace('/\s.*$/'.$u, '', $substr($text, $num_chars));
							} else {
								$text = rtrim($substr($text, 0, $max - $total));
							}
						}

						$total += $num_chars;

					} else { // XHTML_CHOP_WORDS

						if (($num_words = count($words = array_filter(explode(' ', $text), $nonempty))) > 0) {

							if ($total + $num_words > $max) {
								$done = true;
								$words = array_slice($words, 0, $max - $total);
							}

							$total += $num_words;
							$text = implode(' ', $words);
						}
					}

					$ret .= $text;
				}

				if (!$tag) {
					break;
				}

				if ($matches[1][0][0] != '/') {
					$stack[] = $matches[1][0];
				} else {

					if (empty($stack) and !$done) {
						$html = $substr($html, $tag_start + $strlen($tag));
						continue;
					}

					while ($close_tag = array_pop($stack)) {
						if ($strtolower($close_tag) == $strtolower($substr($matches[1][0], 1))) {
							break;
						} else {
							$ret .= "</$close_tag>";
						}
					}
				}

				if ($tag_start and $html[$tag_start - 1] == ' ') {
					$ret .= ' ';
				}

				$ret .= $tag;
				$html = $substr($html, $tag_start + $strlen($tag));
			}

			if ($mode & XHTML_CHOP_NO_EMPTY_TAGS and $substr(rtrim($ret, ' '), -1) == '>') {
				array_pop($stack);
				$ret = $substr($ret, 0, $strrpos($ret, '<'));
			}

			if ($done and ~$mode & XHTML_CHOP_ELIPSIS_OUT) {
				$ret .= $elipsis;
			}

			if (!empty($stack)) {

				$self_closing = array('img', 'input', 'br', 'hr', 'link', 'base', 'meta');

				while ($tag = array_pop($stack)) {
					if (!in_array($strtolower($tag), $self_closing)) {
						$ret .= "</$tag>";
					}
				}
			}

			if ($done and $mode & XHTML_CHOP_ELIPSIS_OUT) {
				$ret .= $elipsis;
			}

			if ($multibyte) {
				mb_internal_encoding($old_encoding);
			}

			return strtr(
				$ret,
				array(
					self::unichr(8204) => '&zwnj;',
					self::unichr(8205) => '&zwj;',
					self::unichr(8206) => '&lrm;',
					self::unichr(8207) => '&rlm;'
			));
		}

		/**
		 * Unicode version of chr()
		 *
		 * @param	int		$code
		 * @return	string
		 */
		protected static function unichr($code) {
			if ($code < 0x80) {
				return  chr($dec);
			} elseif ($code < 0x800) {
				return  chr(0xc0 + (($code - ($code % 0x40)) / 0x40)) .
						chr(0x80 + ($code % 0x40));
			} else {
				return	chr(0xe0 + (($code - ($code % 0x1000)) / 0x1000)) .
						chr(0x80 + ((($code % 0x1000) - ($code % 0x40)) / 0x40)) .
						chr(0x80 + ($code % 0x40));
			}
		}
	}

#EOF