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
	<span>Lorem ipsum dolor <em>sit</em>.</span>
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

Pitfalls
--------

For now, Nokogori won't work properly with string that are not enclosed
in a proper tag, like that:

```xml
<p>
	Some unenclosed string
	<span>Lorem ipsum</span>
</p>
```

Notes
-----

The implementation is probably shitty, as I don't know anything about writing a
decent parser...

Also, the implementation of the parser itself is kind of tied to the class using
it. It is obviously bad but it works :grin:
