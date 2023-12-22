<?php

namespace Ozoriotsn\HealthCheckCmd\Commands;

interface HealthCheckInterface
{

    public function emailReporterErrorApi();

    public function testServerConnection();

    public function testDatabaseConnection();

    public function testSmtpConnection();

    public function testRedisConnection();

    public function testAwsConnection();

    public function testAwsS3Connection();

    public function testAwsSqsConnection();

    public function testConnection($connectionName);

    public function handle();

}
