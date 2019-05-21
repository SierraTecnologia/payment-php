<?php

namespace SierraTecnologia;

/*
 * These tests should really be part of `FileUploadTest`, but because the file creation requests
 * use a different host, the tests for these methods need their own setup and teardown methods.
 */
class FileUploadCreationTest extends TestCase
{
    /**
     * @before
     */
    public function setUpUploadBase()
    {
        SierraTecnologia::$apiUploadBase = SierraTecnologia::$apiBase;
        SierraTecnologia::$apiBase = null;
    }

    /**
     * @after
     */
    public function tearDownUploadBase()
    {
        SierraTecnologia::$apiBase = SierraTecnologia::$apiUploadBase;
        SierraTecnologia::$apiUploadBase = 'https://files.sierratecnologia.com.br';
    }

    public function testIsCreatableWithFileHandle()
    {
        $this->expectsRequest(
            'post',
            '/v1/files',
            null,
            ['Content-Type: multipart/form-data'],
            true,
            SierraTecnologia::$apiUploadBase
        );
        $fp = fopen(dirname(__FILE__) . '/../data/test.png', 'r');
        $resource = FileUpload::create([
            "purpose" => "dispute_evidence",
            "file" => $fp,
            "file_link_data" => ["create" => true]
        ]);
        $this->assertInstanceOf("SierraTecnologia\\FileUpload", $resource);
    }

    public function testIsCreatableWithCurlFile()
    {
        if (!class_exists('\CurlFile', false)) {
            // Older PHP versions don't support this
            return;
        }

        $this->expectsRequest(
            'post',
            '/v1/files',
            null,
            ['Content-Type: multipart/form-data'],
            true,
            SierraTecnologia::$apiUploadBase
        );
        $curlFile = new \CurlFile(dirname(__FILE__) . '/../data/test.png');
        $resource = FileUpload::create([
            "purpose" => "dispute_evidence",
            "file" => $curlFile,
            "file_link_data" => ["create" => true]
        ]);
        $this->assertInstanceOf("SierraTecnologia\\FileUpload", $resource);
    }
}
