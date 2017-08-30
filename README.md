Nokogiri
========

Cuts through XML like a breeze.

Examples
--------

Given this XML:

```xml
<p>
	<span>Lorem ipsum dolor <em>sit amet</em>.</span>
</p>
```

Cutting it at the twentieth character...

```php
$Nokogiri = new Nokogiri\Nokogiri();
$Nokogiri->cut($xml, 20);
```

Would return:

```xml
<p>
	<span>Lorem ipsum dolor <em>sit</em></span>
</p>
```

Cutting it at the eleventh character...

```php
$Nokogiri->cut($xml, 11);
```

Would return:

```xml
<p>
	<span>Lorem ipsum</span>
</p>
```

Note that the blank characters between tags are not taken into account.

Contributing
------

### Installation

Clone the project and run `composer install`.

### Running tests

Run tests with `composer run-script test`.

Notes
-----

The implementation is probably shitty, as I don't know anything about writing a
decent parser...

Also, the implementation of the parser itself is kind of tied to the class using
it. It is obviously bad but it works :grin:
