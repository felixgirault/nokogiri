<?php

use PHPUnit\Framework\TestCase;



/**
 *
 */
class NokogiriTest extends TestCase {

	//
	public $Nokogiri = null;

	/**
	 *
	 */
	public function setUp(): void {
		$this->Nokogiri = new Nokogiri\Nokogiri();
	}

	/**
	 *
	 */
	public function testCut(): void {
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
			$expected,
			$this->Nokogiri->cut($xml, 10)
		);
	}

	/**
	 *
	 */
	public function testCutWithoutTrimming(): void {
		$xml = '<p> <span> Text</span></p>';
		$expected = '<p> <span> Te</span></p>';

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 4, false)
		);
	}

	/**
	 *
	 */
	public function testCutWithTextNode(): void {
		$xml = '<p>This <em>is</em> a text node</p>';
		$expected = '<p>This <em>is</em> a</p>';

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 8)
		);
	}

	/**
	 *
	 */
	public function testCutEllipsis(): void {
		$xml = '<p>This <em>is</em> a text that should not have any dots added</p>';
		$expected = '<p>This <em>is</em> a text that should not have any dots added</p>';
		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 200, true, '…')
		);

		$xml = '<p>This <em>is</em> a text that should have dots added</p>';
		$expected = '<p>This <em>is</em> a text…</p>';
		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 13, true, '…')
		);

		$xml = '<p><span>Text</span></p>';
		$expected = '<p><span>Te</span>…</p>';
		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 2, true, '…')
		);
	}

	/**
	 *
	 */
	public function testCutWithAutoClosingTags(): void {
		$xml = '<p>Lorem<br /> ipsum dolor sit amet</p>';
		$expected = '<p>Lorem<br /> ipsum</p>';

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 11)
		);

		$xml = '<p>Lorem<br/> ipsum dolor sit amet</p>';
		$expected = '<p>Lorem<br/> ipsum</p>';

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 11)
		);
	}

	public function testAttributeValues(): void {
		$html = <<<HTML
			<p><a href="www.attribu.te/with/slash">this is a link</a></p>
HTML;
		$expected = <<<HTML
			<p><a href="www.attribu.te/with/slash">this</a></p>
HTML;
		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($html, 4, true)
		);

		$html = <<<HTML
			<p><a title='simple quotes' aria-label="double quotes">this is a link</a></p>
HTML;
		$expected = <<<HTML
			<p><a title='simple quotes' aria-label="double quotes">this</a></p>
HTML;
		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($html, 4, true)
		);
	}

	/**
	 *
	 */
	public function testDocx(): void {
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

		$this->assertEquals(
			$expected,
			$this->Nokogiri->cut($xml, 5)
		);
	}
}
