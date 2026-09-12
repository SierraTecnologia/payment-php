<?php

namespace SierraTecnologia;

class SierraTecnologiaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->saveOriginalValues();
    }

    protected function tearDown(): void
    {
        $this->restoreOriginalValues();
        parent::tearDown();
    }

    public function saveOriginalValues()
    {
        $this->orig = [
            'caBundlePath' => SierraTecnologia::$caBundlePath,
        ];
    }

    public function restoreOriginalValues()
    {
        SierraTecnologia::$caBundlePath = $this->orig['caBundlePath'];
    }

    public function testCABundlePathAccessors()
    {
        SierraTecnologia::setCABundlePath('path/to/ca/bundle');
        $this->assertEquals('path/to/ca/bundle', SierraTecnologia::getCABundlePath());
    }
}
