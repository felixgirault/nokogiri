<?php

namespace Nokogiri;

use Nokogiri\Parser;



/**
 *
 */
class Nokogiri {

	/**
	 *
	 */
	public function cut($xml, $limit) {
		$Parser = new Parser();
		$opened = [];
		$position = 0;

		$Parser->on(
			Parser::OPENED_TAG_EVENT,
			function($tag) use (&$opened) {
				$opened[] = $tag;
			}
		);

		$Parser->on(
			Parser::PARSED_TAG_CONTENTS_EVENT,
			function($contents, $i) use (&$opened, &$count, &$position, $limit) {
				if ($this->_isWhitespace($contents)) {
					return;
				}

				$count += strlen($contents);

				if ($count >= $limit) {
					$position = $i - ($count - $limit);
					return false;
				}
			}
		);

		$Parser->on(
			Parser::CLOSED_TAG_EVENT,
			function() use (&$opened) {
				array_pop($opened);
			}
		);

		$Parser->parse($xml);

		return $position
			? $this->_enclose($xml, $position, $opened)
			: $xml;
	}

	/**
	 *
	 */
	protected function _isWhitespace($string) {
		return preg_match('~\s+~i', $string);
	}

	/**
	 *
	 */
	protected function _enclose($xml, $position, $tags) {
		$xml = substr($xml, 0, $position);

		while ($tag = array_pop($tags)) {
			$xml .= "</$tag>";
		}

		return $xml;
	}
}
