<?php

namespace SierraTecnologia;

class FileTest extends TestCase
{
    const TEST_RESOURCE_ID = 'file_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/files'
        );
        $resources = File::all();
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\File", $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/files/' . self::TEST_RESOURCE_ID
        );
        $resource = File::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\File", $resource);
    }

    public function testDeserializesFromFile()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'file',
        ], null);
        $this->assertInstanceOf("SierraTecnologia\\File", $obj);
    }

    public function testDeserializesFromFileUpload()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject([
            'object' => 'file_upload',
        ], null);
        $this->assertInstanceOf("SierraTecnologia\\File", $obj);
    }
}
