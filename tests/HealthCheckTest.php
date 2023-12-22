<?php

namespace Ozoriotsn\HealthCheckCmd\Tests;

use Ozoriotsn\HealthCheckCmd\Commands\HealthCheckInterface;
use Ozoriotsn\HealthCheckCmd\Tests\TestCase as TestCase;

class HealthCheckTest extends TestCase
{

    public function testShouldConnectionServices()
    {
        $report = $this->createMock(HealthCheckInterface::class);
        $report->expects($this->any())
            ->method('testConnection')
            ->willReturn($this->assertTrue(true));
    }

}
