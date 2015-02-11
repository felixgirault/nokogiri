<?php

use PHPUnit_Framework_TestCase as TestCase;



/**
 *
 */
class NokogiriTest extends TestCase {

	//
	public $Nokogiri = null;

	/**
	 *
	 */
	public function setUp() {
		$this->Nokogiri = new Nokogiri\Nokogiri();
	}

	/**
	 *
	 */
	public function testCut() {
		$xml = <<<XML
			<p>
				<span class="a">Text</span>
				<span class="b"> Text </span>
				<span>
					<span>Text</span>
					<span>Text</span>
				</span>
			</p>
XML;

		$expected = <<<XML
			<p>
				<span class="a">Text</span>
				<span class="b"> Text </span>
				<span>
					<span>Te</span></span></p>
XML;

		$this->assertEquals(
			new SimpleXMLElement($expected),
			new SimpleXMLElement($this->Nokogiri->cut($xml, 10))
		);
	}

	/**
	 *
	 */
	public function testCutWithoutTrimming() {
		$xml = '<p> <span> Text</span></p>';
		$expected = '<p> <span> Te</span></p>';

		$this->assertEquals(
			new SimpleXMLElement($expected),
			new SimpleXMLElement($this->Nokogiri->cut($xml, 4, false))
		);
	}

	/**
	 *
	 */
	public function testCutWithTextNode() {
		$xml = '<p>This <em>is</em> a text node</p>';
		$expected = '<p>This <em>is</em> a</p>';

		$this->assertEquals(
			new SimpleXMLElement($expected),
			new SimpleXMLElement($this->Nokogiri->cut($xml, 8))
		);
	}

	/**
	 *
	 */
	public function testCutWithAutoClosingTags() {
		$xml = '<p>Lorem<br /> ipsum dolor sit amet</p>';
		$expected = '<p>Lorem<br /> ipsum</p>';

		$this->assertEquals(
			new SimpleXMLElement($expected),
			new SimpleXMLElement($this->Nokogiri->cut($xml, 11))
		);

		$xml = '<p>Lorem<br/> ipsum dolor sit amet</p>';
		$expected = '<p>Lorem<br/> ipsum</p>';

		$this->assertEquals(
			new SimpleXMLElement($expected),
			new SimpleXMLElement($this->Nokogiri->cut($xml, 11))
		);
	}

	/**
	 *
	 */
	public function testDocx() {
		$xml = <<<XML
			<w:r>
				<w:rPr>
					<w:b w:val="1" />
				</w:rPr>
				<w:t xml:space="preserve">lorem ipsum</w:t>
			</w:r>
XML;

		$expected = <<<XML
			<w:r>
				<w:rPr>
					<w:b w:val="1" />
				</w:rPr>
				<w:t xml:space="preserve">lorem</w:t></w:r>
XML;

		// the "w" namespace in not defined, but don't quite care here, hence
		// the use of LIBXML_NOERROR
		$this->assertEquals(
			new SimpleXMLElement($expected, LIBXML_NOERROR),
			new SimpleXMLElement($this->Nokogiri->cut($xml, 5), LIBXML_NOERROR)
		);
	}
}
