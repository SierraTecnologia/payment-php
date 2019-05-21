<?php

namespace SierraTecnologia;

class SierraTecnologiaTest extends TestCase
{
    /**
     * @before
     */
    public function saveOriginalValues()
    {
        $this->orig = [
            'caBundlePath' => SierraTecnologia::$caBundlePath,
        ];
    }

    /**
     * @after
     */
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
