<?php

namespace Nokogiri;



/**
 *
 */
class Parser {

	//
	const OPENED_TAG_EVENT = 0;

	//
	const PARSED_TAG_CONTENTS_EVENT = 1;

	//
	const CLOSED_TAG_EVENT = 2;

	//
	const PARSING_OPENING_TAG = 0;

	//
	const PARSING_TAG_ATTRIBUTES = 1;

	//
	const PARSING_TAG_CONTENTS = 2;

	//
	const PARSING_CLOSING_TAG = 3;

	//
	const PARSING_SELF_CLOSING_TAG = 4;

	//
	protected $_observers = [];

	//
	protected $_continue = true;

	//
	protected $_contents = '';

	//
	protected $_tagName = '';

	//
	protected $_state = self::PARSING_TAG_CONTENTS;

	/**
	 *
	 */
	public function on($event, callable $callback) {
		$this->_observers[$event] = $callback;
	}

	/**
	 *
	 */
	protected function _emit($event) {
		if (isset($this->_observers[$event])) {
			$continue = call_user_func_array(
				$this->_observers[$event],
				array_slice(func_get_args(), 1)
			);

			$this->_continue = ($continue !== false);
		}
	}

	/**
	 *
	 */
	public function parse($xml) {
		for ($i = 0; $i < strlen($xml); $i++) {
			$char = $xml[$i];

			switch ($this->_state) {
				case self::PARSING_OPENING_TAG:
					$this->_parseOpeningTag($char);
					break;

				case self::PARSING_TAG_ATTRIBUTES:
					$this->_parseTagAttributes($char);
					break;

				case self::PARSING_TAG_CONTENTS:
					$this->_parseTagContents($char, $i);
					break;

				case self::PARSING_CLOSING_TAG:
					$this->_parseClosingTag($char);
					break;

				case self::PARSING_SELF_CLOSING_TAG:
					$this->_parseSelfClosingTag($char);
					break;
			}

			if (!$this->_continue) {
				break;
			}
		}
	}

	/**
	 *
	 */
	protected function _parseOpeningTag($char) {
		switch ($char) {
			case '/':
				$this->_state = empty($this->_tagName)
					? self::PARSING_CLOSING_TAG
					: self::PARSING_SELF_CLOSING_TAG;

				$this->_tagName = '';
				break;

			case ' ':
				$this->_state = self::PARSING_TAG_ATTRIBUTES;
				break;

			case '>':
				$this->_state = self::PARSING_TAG_CONTENTS;
				$this->_emit(self::OPENED_TAG_EVENT, $this->_tagName);
				$this->_tagName = '';
				break;

			default:
				$this->_tagName .= $char;
				break;
		}
	}

	/**
	 *
	 */
	protected function _parseTagAttributes($char) {
		switch ($char) {
			case '/':
				$this->_state = self::PARSING_SELF_CLOSING_TAG;
				break;

			case '>':
				$this->_state = self::PARSING_TAG_CONTENTS;
				$this->_emit(self::OPENED_TAG_EVENT, $this->_tagName);
				$this->_tagName = '';
				break;
		}
	}

	/**
	 *
	 */
	protected function _parseTagContents($char, $i) {
		switch ($char) {
			case '<':
				$this->_state = self::PARSING_OPENING_TAG;
				$this->_emit(self::PARSED_TAG_CONTENTS_EVENT, $this->_contents, $i);
				$this->_contents = '';
				break;

			default:
				$this->_contents .= $char;
				break;
		}
	}

	/**
	 *
	 */
	protected function _parseClosingTag($char) {
		switch ($char) {
			case '>':
				$this->_state = self::PARSING_TAG_CONTENTS;
				$this->_emit(self::CLOSED_TAG_EVENT, $this->_tagName);
				$this->_tagName = '';
				break;
		}
	}

	/**
	 *
	 */
	protected function _parseSelfClosingTag($char) {
		switch ($char) {
			case '>':
				$this->_state = self::PARSING_TAG_CONTENTS;
				$this->_emit(self::OPENED_TAG_EVENT, $this->_tagName);
				$this->_emit(self::CLOSED_TAG_EVENT, $this->_tagName);
				$this->_tagName = '';
				break;

			default:
				$this->_tagName .= $char;
				break;
		}
	}
}
