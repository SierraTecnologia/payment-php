<?php

namespace SierraTecnologia;

class FileUploadTest extends TestCase
{
    const TEST_RESOURCE_ID = 'file_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/files'
        );
        $resources = FileUpload::all();
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\FileUpload", $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/files/' . self::TEST_RESOURCE_ID
        );
        $resource = FileUpload::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\FileUpload", $resource);
    }

    public function testDeserializesFromFile()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject(
            [
            'object' => 'file',
            ], null
        );
        $this->assertInstanceOf("SierraTecnologia\\FileUpload", $obj);
    }

    public function testDeserializesFromFileUpload()
    {
        $obj = Util\Util::convertToSierraTecnologiaObject(
            [
            'object' => 'file_upload',
            ], null
        );
        $this->assertInstanceOf("SierraTecnologia\\FileUpload", $obj);
    }
}
