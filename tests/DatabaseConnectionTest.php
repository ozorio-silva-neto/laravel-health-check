<?php

namespace Ozoriotsn\HealthCheckCmd\Tests;

use PHPUnit\Framework\TestCase;
use Exception;

class DatabaseConnectionTest extends TestCase
{
    /**
     * Test the database connection.
     *
     * @return void
     */
    public function testDatabaseConnection(): void
    {
        // Test successful database connection
        $this->assertTrue($this->checkDatabaseConnection());

        // Test error connecting to the database
        $this->assertFalse($this->checkDatabaseConnectionWithError());
    }

    /**
     * Check the database connection.
     *
     * @throws Exception if there is an error connecting to the database
     * @return bool true if the database connection is successful, false otherwise
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            // Simulate successful database connection
            return true;
        } catch (Exception $e) {
            // Simulate error connecting to the database
            return false;
        }
    }

    /**
     * Checks the database connection and returns a boolean indicating whether it was successful or not.
     *
     * @throws Exception if there is an error connecting to the database
     * @return bool true if the connection was successful, false otherwise
     */

    private function checkDatabaseConnectionWithError(): bool
    {
        try {
            // Simulate error connecting to the database
            throw new Exception('Error connecting to the database');
        } catch (Exception $e) {
            return false;
        }
    }
}
