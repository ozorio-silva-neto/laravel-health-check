<?php

namespace Ozoriotsn\HealthCheckCmd\Tests;

use Exception;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;



class SmtpConnectionTest extends TestCase
{
    public $port = 2525;
    public $host = 'sandbox.smtp.mailtrap.io'; //https://mailtrap.io
    public $invalidHost = '127.0.0.1';
    public $invalidPort = 123456;
    public $invalidUsername = 'invalid_username';
    public $invalidPassword = 'invalid_password';
    public $username = 'cahnged_username';
    public $password = 'changed_password';

    /**
     * Test the SMTP connection with different credentials.
     *
     * @return void
     */
    public function testSmtpConnection(): void
    {
        // Test SMTP connection with valid credentials
        $this->assertTrue($this->smtpConnectionTestHelper($this->host, $this->port, $this->username, $this->password));

        // Test SMTP connection with invalid host
        $this->assertFalse($this->smtpConnectionTestHelper($this->invalidHost, $this->port, $this->username, $this->password));

        // Test SMTP connection with invalid port
        $this->assertFalse($this->smtpConnectionTestHelper($this->host, $this->invalidPort, $this->username, $this->password));

        // Test SMTP connection with invalid username
        $this->assertFalse($this->smtpConnectionTestHelper($this->host, $this->port, $this->invalidUsername, $this->password));

        // Test SMTP connection with invalid password
        $this->assertFalse($this->smtpConnectionTestHelper($this->host, $this->port, $this->username, $this->invalidPassword));

        // Test SMTP connection with all invalid credentials
        $this->assertFalse($this->smtpConnectionTestHelper($this->invalidHost, $this->invalidPort, $this->invalidUsername, $this->invalidPassword));
    }

    /**
     * Tests the SMTP connection to a given host and port with the provided credentials.
     *
     * @param string $host The hostname or IP address of the SMTP server.
     * @param int $port The port number to connect to on the SMTP server.
     * @param string $username The username for authenticating with the SMTP server.
     * @param string $password The password for authenticating with the SMTP server.
     * @throws Exception If an error occurs while testing the SMTP connection.
     * @return bool Returns true if the SMTP connection is successful, false otherwise.
     */

    private function smtpConnectionTestHelper(string $host, int $port, string $username, string $password): bool
    {
        try {
            $transport = new EsmtpTransport($host, $port);
            $transport->setUsername($username);
            $transport->setPassword($password);
            $transport->start();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
