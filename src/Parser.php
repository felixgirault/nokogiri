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
			// we're in fact parsing a closing tag
			case '/':
				$this->_state = self::PARSING_CLOSING_TAG;
				break;

			// we're beggining to parse attributes
			// we know the name of the tag we just opened
			case ' ':
				$this->_state = self::PARSING_TAG_ATTRIBUTES;
				$this->_emit(self::OPENED_TAG_EVENT, $this->_tagName);
				$this->_tagName = '';
				break;

			// we're reaching the end of an opening tag
			// we know the name of the tag we just opened
			// we can begin to store the tag's contents for later use
			case '>':
				$this->_state = self::PARSING_TAG_CONTENTS;
				$this->_emit(self::OPENED_TAG_EVENT, $this->_tagName);
				$this->_tagName = '';
				break;

			// we're storing the tag's name for later use
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
			// we're reaching the end of an opening tag
			// we can begin to store the tag's contents for later use
			case '>':
				$this->_state = self::PARSING_TAG_CONTENTS;
				$this->_contents = '';
				break;
		}
	}

	/**
	 *
	 */
	protected function _parseTagContents($char, $i) {
		switch ($char) {
			// we're reaching the start of a tag
			// we can begin to store the tag's name for later use
			case '<':
				$this->_state = self::PARSING_OPENING_TAG;
				$this->_emit(self::PARSED_TAG_CONTENTS_EVENT, $this->_contents, $i);
				$this->_contents = '';
				break;

			// we're storing the tag's contents for later use
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
			// we're reaching the end of a closing tag
			case '>':
				$this->_state = self::PARSING_TAG_CONTENTS;
				$this->_emit(self::CLOSED_TAG_EVENT);
				break;
		}
	}
}
