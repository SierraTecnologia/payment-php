<?php

namespace SierraTecnologia;

class SierraTecnologiaObjectTest extends TestCase
{
    /**
     * @before
     */
    public function setUpReflectors()
    {
        // Sets up reflectors needed by some tests to access protected or
        // private attributes.

        // This is used to invoke the `deepCopy` protected function
        $this->deepCopyReflector = new \ReflectionMethod('SierraTecnologia\\SierraTecnologiaObject', 'deepCopy');
        $this->deepCopyReflector->setAccessible(true);

        // This is used to access the `_opts` protected variable
        $this->optsReflector = new \ReflectionProperty('SierraTecnologia\\SierraTecnologiaObject', '_opts');
        $this->optsReflector->setAccessible(true);
    }

    public function testArrayAccessorsSemantics()
    {
        $s = new SierraTecnologiaObject();
        $s['foo'] = 'a';
        $this->assertSame($s['foo'], 'a');
        $this->assertTrue(isset($s['foo']));
        unset($s['foo']);
        $this->assertFalse(isset($s['foo']));
    }

    public function testNormalAccessorsSemantics()
    {
        $s = new SierraTecnologiaObject();
        $s->foo = 'a';
        $this->assertSame($s->foo, 'a');
        $this->assertTrue(isset($s->foo));
        unset($s->foo);
        $this->assertFalse(isset($s->foo));
    }

    public function testArrayAccessorsMatchNormalAccessors()
    {
        $s = new SierraTecnologiaObject();
        $s->foo = 'a';
        $this->assertSame($s['foo'], 'a');

        $s['bar'] = 'b';
        $this->assertSame($s->bar, 'b');
    }

    public function testCount()
    {
        $s = new SierraTecnologiaObject();
        $this->assertSame(0, count($s));

        $s['key1'] = 'value1';
        $this->assertSame(1, count($s));

        $s['key2'] = 'value2';
        $this->assertSame(2, count($s));

        unset($s['key1']);
        $this->assertSame(1, count($s));
    }

    public function testKeys()
    {
        $s = new SierraTecnologiaObject();
        $s->foo = 'bar';
        $this->assertSame($s->keys(), ['foo']);
    }

    public function testValues()
    {
        $s = new SierraTecnologiaObject();
        $s->foo = 'bar';
        $this->assertSame($s->values(), ['bar']);
    }

    public function testToArray()
    {
        $s = new SierraTecnologiaObject();
        $s->foo = 'a';

        $converted = $s->__toArray();

        $this->assertInternalType('array', $converted);
        $this->assertArrayHasKey('foo', $converted);
        $this->assertEquals('a', $converted['foo']);
    }

    public function testRecursiveToArray()
    {
        $s = new SierraTecnologiaObject();
        $z = new SierraTecnologiaObject();

        $s->child = $z;
        $z->foo = 'a';

        $converted = $s->__toArray(true);

        $this->assertInternalType('array', $converted);
        $this->assertArrayHasKey('child', $converted);
        $this->assertInternalType('array', $converted['child']);
        $this->assertArrayHasKey('foo', $converted['child']);
        $this->assertEquals('a', $converted['child']['foo']);
    }

    public function testNonexistentProperty()
    {
        $s = new SierraTecnologiaObject();
        $this->assertNull($s->nonexistent);
    }

    public function testPropertyDoesNotExists()
    {
        $s = new SierraTecnologiaObject();
        $this->assertNull($s['nonexistent']);
    }

    public function testJsonEncode()
    {
        $s = new SierraTecnologiaObject();
        $s->foo = 'a';

        $this->assertEquals('{"foo":"a"}', json_encode($s));
    }

    public function testToString()
    {
        $s = new SierraTecnologiaObject();
        $s->foo = 'a';

        $string = $s->__toString();
        $expected = <<<EOS
SierraTecnologia\SierraTecnologiaObject JSON: {
    "foo": "a"
}
EOS;
        $this->assertEquals($expected, $string);
    }

    public function testReplaceNewNestedUpdatable()
    {
        $s = new SierraTecnologiaObject();

        $s->metadata = ['bar'];
        $this->assertSame($s->metadata, ['bar']);
        $s->metadata = ['baz', 'qux'];
        $this->assertSame($s->metadata, ['baz', 'qux']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPermanentAttribute()
    {
        $s = new SierraTecnologiaObject();
        $s->id = 'abc_123';
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEmptyStringValue()
    {
        $s = new SierraTecnologiaObject();
        $s->foo = '';
    }

    public function testSerializeParametersOnEmptyObject()
    {
        $obj = SierraTecnologiaObject::constructFrom([]);
        $this->assertSame([], $obj->serializeParameters());
    }

    public function testSerializeParametersOnNewObjectWithSubObject()
    {
        $obj = new SierraTecnologiaObject();
        $obj->metadata = ['foo' => 'bar'];
        $this->assertSame(['metadata' => ['foo' => 'bar']], $obj->serializeParameters());
    }

    public function testSerializeParametersOnBasicObject()
    {
        $obj = SierraTecnologiaObject::constructFrom(['foo' => null]);
        $obj->updateAttributes(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $obj->serializeParameters());
    }

    public function testSerializeParametersOnMoreComplexObject()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'foo' => SierraTecnologiaObject::constructFrom(
                [
                'bar' => null,
                'baz' => null,
                ]
            ),
            ]
        );
        $obj->foo->bar = 'newbar';
        $this->assertSame(['foo' => ['bar' => 'newbar']], $obj->serializeParameters());
    }

    public function testSerializeParametersOnArray()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'foo' => null,
            ]
        );
        $obj->foo = ['new-value'];
        $this->assertSame(['foo' => ['new-value']], $obj->serializeParameters());
    }

    public function testSerializeParametersOnArrayThatShortens()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'foo' => ['0-index', '1-index', '2-index'],
            ]
        );
        $obj->foo = ['new-value'];
        $this->assertSame(['foo' => ['new-value']], $obj->serializeParameters());
    }

    public function testSerializeParametersOnArrayThatLengthens()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'foo' => ['0-index', '1-index', '2-index'],
            ]
        );
        $obj->foo = array_fill(0, 4, 'new-value');
        $this->assertSame(['foo' => array_fill(0, 4, 'new-value')], $obj->serializeParameters());
    }

    public function testSerializeParametersOnArrayOfHashes()
    {
        $obj = SierraTecnologiaObject::constructFrom(['foo' => null]);
        $obj->foo = [
            SierraTecnologiaObject::constructFrom(['bar' => null]),
        ];

        $obj->foo[0]->bar = 'baz';
        $this->assertSame(['foo' => [['bar' => 'baz']]], $obj->serializeParameters());
    }

    public function testSerializeParametersDoesNotIncludeUnchangedValues()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'foo' => null,
            ]
        );
        $this->assertSame([], $obj->serializeParameters());
    }

    public function testSerializeParametersOnUnchangedArray()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'foo' => ['0-index', '1-index', '2-index'],
            ]
        );
        $obj->foo = ['0-index', '1-index', '2-index'];
        $this->assertSame([], $obj->serializeParameters());
    }

    public function testSerializeParametersWithSierraTecnologiaObject()
    {
        $obj = SierraTecnologiaObject::constructFrom([]);
        $obj->metadata = SierraTecnologiaObject::constructFrom(['foo' => 'bar']);

        $serialized = $obj->serializeParameters();
        $this->assertSame(['foo' => 'bar'], $serialized['metadata']);
    }

    public function testSerializeParametersOnReplacedSierraTecnologiaObject()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'source' => SierraTecnologiaObject::constructFrom(['bar' => 'foo']),
            ]
        );
        $obj->source = SierraTecnologiaObject::constructFrom(['baz' => 'foo']);

        $serialized = $obj->serializeParameters();
        $this->assertSame(['baz' => 'foo'], $serialized['source']);
    }

    public function testSerializeParametersOnReplacedSierraTecnologiaObjectWhichIsMetadata()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'metadata' => SierraTecnologiaObject::constructFrom(['bar' => 'foo']),
            ]
        );
        $obj->metadata = SierraTecnologiaObject::constructFrom(['baz' => 'foo']);

        $serialized = $obj->serializeParameters();
        $this->assertSame(['bar' => '', 'baz' => 'foo'], $serialized['metadata']);
    }

    public function testSerializeParametersOnArrayOfSierraTecnologiaObjects()
    {
        $obj = SierraTecnologiaObject::constructFrom([]);
        $obj->metadata = [
            SierraTecnologiaObject::constructFrom(['foo' => 'bar']),
        ];

        $serialized = $obj->serializeParameters();
        $this->assertSame([['foo' => 'bar']], $serialized['metadata']);
    }

    public function testSerializeParametersOnSetApiResource()
    {
        $customer = Customer::constructFrom(['id' => 'cus_123']);
        $obj = SierraTecnologiaObject::constructFrom([]);

        // the key here is that the property is set explicitly (and therefore
        // marked as unsaved), which is why it gets included below
        $obj->customer = $customer;

        $serialized = $obj->serializeParameters();
        $this->assertSame(['customer' => $customer], $serialized);
    }

    public function testSerializeParametersOnNotSetApiResource()
    {
        $customer = Customer::constructFrom(['id' => 'cus_123']);
        $obj = SierraTecnologiaObject::constructFrom(['customer' => $customer]);

        $serialized = $obj->serializeParameters();
        $this->assertSame([], $serialized);
    }

    public function testSerializeParametersOnApiResourceFlaggedWithSaveWithParent()
    {
        $customer = Customer::constructFrom(['id' => 'cus_123']);
        $customer->saveWithParent = true;

        $obj = SierraTecnologiaObject::constructFrom(['customer' => $customer]);

        $serialized = $obj->serializeParameters();
        $this->assertSame(['customer' => []], $serialized);
    }

    public function testSerializeParametersRaisesExceotionOnOtherEmbeddedApiResources()
    {
        // This customer doesn't have an ID and therefore the library doesn't know
        // what to do with it and throws an InvalidArgumentException because it's
        // probably not what the user expected to happen.
        $customer = Customer::constructFrom([]);

        $obj = SierraTecnologiaObject::constructFrom([]);
        $obj->customer = $customer;

        try {
            $serialized = $obj->serializeParameters();
            $this->fail("Did not raise error");
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                "Cannot save property `customer` containing an API resource of type SierraTecnologia\Customer. " .
                "It doesn't appear to be persisted and is not marked as `saveWithParent`.",
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->fail("Unexpected exception: " . get_class($e));
        }
    }

    public function testSerializeParametersForce()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'id' => 'id',
            'metadata' => SierraTecnologiaObject::constructFrom(
                [
                'bar' => 'foo',
                ]
            ),
            ]
        );

        $serialized = $obj->serializeParameters(true);
        $this->assertSame(['id' => 'id', 'metadata' => ['bar' => 'foo']], $serialized);
    }

    public function testDirty()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'id' => 'id',
            'metadata' => SierraTecnologiaObject::constructFrom(
                [
                'bar' => 'foo',
                ]
            ),
            ]
        );

        // note that `$force` and `dirty()` are for different things, but are
        // functionally equivalent
        $obj->dirty();

        $serialized = $obj->serializeParameters();
        $this->assertSame(['id' => 'id', 'metadata' => ['bar' => 'foo']], $serialized);
    }

    public function testDeepCopy()
    {
        $opts = [
            "api_base" => SierraTecnologia::$apiBase,
            "api_key" => "apikey",
        ];
        $values = [
            "id" => 1,
            "name" => "SierraTecnologia",
            "arr" => [
                SierraTecnologiaObject::constructFrom(["id" => "index0"], $opts),
                "index1",
                2,
            ],
            "map" => [
                "0" => SierraTecnologiaObject::constructFrom(["id" => "index0"], $opts),
                "1" => "index1",
                "2" => 2
            ],
        ];

        $copyValues = $this->deepCopyReflector->invoke(null, $values);

        // we can't compare the hashes directly because they have embedded
        // objects which are different from each other
        $this->assertEquals($values["id"], $copyValues["id"]);
        $this->assertEquals($values["name"], $copyValues["name"]);
        $this->assertEquals(count($values["arr"]), count($copyValues["arr"]));

        // internal values of the copied SierraTecnologiaObject should be the same,
        // but the object itself should be new (hence the assertNotSame)
        $this->assertEquals($values["arr"][0]["id"], $copyValues["arr"][0]["id"]);
        $this->assertNotSame($values["arr"][0], $copyValues["arr"][0]);

        // likewise, the Util\RequestOptions instance in _opts should have
        // copied values but be a new instance
        $this->assertEquals(
            $this->optsReflector->getValue($values["arr"][0]),
            $this->optsReflector->getValue($copyValues["arr"][0])
        );
        $this->assertNotSame(
            $this->optsReflector->getValue($values["arr"][0]),
            $this->optsReflector->getValue($copyValues["arr"][0])
        );

        // scalars however, can be compared
        $this->assertEquals($values["arr"][1], $copyValues["arr"][1]);
        $this->assertEquals($values["arr"][2], $copyValues["arr"][2]);

        // and a similar story with the hash
        $this->assertEquals($values["map"]["0"]["id"], $copyValues["map"]["0"]["id"]);
        $this->assertNotSame($values["map"]["0"], $copyValues["map"]["0"]);
        $this->assertNotSame(
            $this->optsReflector->getValue($values["arr"][0]),
            $this->optsReflector->getValue($copyValues["arr"][0])
        );
        $this->assertEquals(
            $this->optsReflector->getValue($values["map"]["0"]),
            $this->optsReflector->getValue($copyValues["map"]["0"])
        );
        $this->assertNotSame(
            $this->optsReflector->getValue($values["map"]["0"]),
            $this->optsReflector->getValue($copyValues["map"]["0"])
        );
        $this->assertEquals($values["map"]["1"], $copyValues["map"]["1"]);
        $this->assertEquals($values["map"]["2"], $copyValues["map"]["2"]);
    }

    public function testDeepCopyMaintainClass()
    {
        $charge = Charge::constructFrom(["id" => 1], null);
        $copyCharge = $this->deepCopyReflector->invoke(null, $charge);
        $this->assertEquals(get_class($charge), get_class($copyCharge));
    }

    public function testIsDeleted()
    {
        $obj = SierraTecnologiaObject::constructFrom([]);
        $this->assertFalse($obj->isDeleted());

        $obj = SierraTecnologiaObject::constructFrom(['deleted' => false]);
        $this->assertFalse($obj->isDeleted());

        $obj = SierraTecnologiaObject::constructFrom(['deleted' => true]);
        $this->assertTrue($obj->isDeleted());
    }

    public function testDeserializeEmptyMetadata()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'metadata' => [],
            ]
        );

        $this->assertInstanceOf("SierraTecnologia\\SierraTecnologiaObject", $obj->metadata);
    }

    public function testDeserializeMetadataWithKeyNamedMetadata()
    {
        $obj = SierraTecnologiaObject::constructFrom(
            [
            'metadata' => ['metadata' => 'value'],
            ]
        );

        $this->assertInstanceOf("SierraTecnologia\\SierraTecnologiaObject", $obj->metadata);
        $this->assertEquals("value", $obj->metadata->metadata);
    }
}
