<?php

namespace SierraTecnologia\Sigma;

class AuthorizationTest extends \SierraTecnologia\TestCase
{
    const TEST_RESOURCE_ID = 'sqr_123';

    public function testIsListable()
    {
        $resources = ScheduledQueryRun::all();
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\Sigma\\ScheduledQueryRun", $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $resource = ScheduledQueryRun::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\Sigma\\ScheduledQueryRun", $resource);
    }
}
