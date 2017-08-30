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
	public function cut($xml, $limit, $trim = true, $ellipsis = null) {
		$Parser = new Parser();
		$opened = [];
		$count = 0;
		$position = 0;

		$Parser->on(
			Parser::OPENED_TAG_EVENT,
			function($tag) use (&$opened) {
				$opened[] = $tag;
			}
		);

		$Parser->on(
			Parser::PARSED_TAG_CONTENTS_EVENT,
			function($contents, $i) use (&$opened, &$count, &$position, $limit, $trim) {
				$length = strlen($contents);
				$offset = 0;

				if ($length && $trim) {
					$contents = ltrim($contents);
					$offset = $length - strlen($contents);
					$contents = rtrim($contents);
					$length = strlen($contents);
				}

				if ($length === 0) {
					return;
				}

				$count += $length;

				if ($count >= $limit) {
					$position = $i - ($count - $limit) - $offset;
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
			? $this->_enclose($xml, $position, $opened, $ellipsis)
			: $xml;
	}

	/**
	 *
	 */
	protected function _enclose($xml, $position, $tags, $ellipsis = null) {
		$xml = substr($xml, 0, $position);

		while ($tag = array_pop($tags)) {
			if ($ellipsis && count($tags) === 0) {
				$xml .= $ellipsis;
			}
			$xml .= "</$tag>";
		}

		return $xml;
	}
}
