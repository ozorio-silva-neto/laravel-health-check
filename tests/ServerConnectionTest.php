<?php

namespace Ozoriotsn\HealthCheckCmd\Tests;

use Exception;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ServerConnectionTest extends TestCase
{

    /**
     * Test the server connection.
     * @test
     * @dataProvider provider
     * @param mixed $expectedResult The expected result.
     * @param mixed $input The input data.
     * @throws Exception If the server connection check fails.
     * @return void
     */
    public function testServerConnection($expectedResult, $input)
    {

        if ($expectedResult) {
            $this->assertTrue($this->checkServerConnection($input));
        } else {
            $this->assertFalse($this->checkServerConnection($input));
        }

    }

    /**
     * Check the server connection by making a GET request to the given URL.
     *
     * @param string $url The URL to check the server connection.
     * @throws Exception If there is an error while making the request.
     * @return bool Returns true if the server connection is successful, false otherwise.
     */
    private function checkServerConnection($url)
    {
        try {
            $client = new Client();
            $client->request('GET', $url);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generates the function comment for the given function body.
     *
     * @return array An array of test data pairs.
     */
    public function provider()
    {
        return [
            [true, "127.0.0.1"],
            [false, "127.0.0.1/not-exist"]

        ];
    }
}
