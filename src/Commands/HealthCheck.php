<?php

namespace Ozoriotsn\HealthCheckCmd\Commands;

use Exception;
use Aws\Sqs\SqsClient;
use Aws\Sts\StsClient;
use Illuminate\Mail\Message;
use Illuminate\Console\Command;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Ozoriotsn\HealthCheckCmd\Commands\HealthCheckInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Console\Command\Command as CommandAlias;


class HealthCheck extends Command implements HealthCheckInterface
{

    /**
     * The name and signature of the console command.
     *
     * @var		string	$signature
     */
    protected $signature = 'health-check-cmd:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Healthcheck Api CMD';

    public string $channel;

    /**
     * Creates a new instance of the PHP class.
     *
     * @return void
     */
    public function __construct()
    {
        $this->channel = 'emergency';
        parent::__construct();
    }

    /**
     * Sends an error report email using the configured API.
     *
     * @param mixed $error The error message or object.
     * @param mixed $service The service name related to the error.
     * @param mixed $message The additional message to include in the email.
     * @throws Exception A description of the exception that might be thrown.
     * @return SentMessage|null The sent message object or null if the email failed to send.
     */
    public function emailReporterErrorApi($error = null, $service = null, $message = null): ?SentMessage
    {
        $env = config('app.env');
        $name = config('app.name');
        $subject = "{$service} - {$name}:{$env}  {$message}";

        return Mail::html("<h1>{$subject}</h1> <hr /> <pre>{$error}</pre>", function (Message $message) use ($subject) {
            $message->subject($subject)
                ->to(config('app.email_report_error_api'))
                ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Tests the server connection.
     *
     * @throws Exception if there is a server connection error.
     * @return bool true if the server connection is successful, false otherwise.
     */
    public function testServerConnection(): bool
    {
        try {
            $url = 'http://' . request()->getHttpHost();
            Http::get($url);
            return true;
        } catch (Exception $e) {
            Log::channel($this->channel)->critical('Server Connection Error: ' . $e);
            $this->emailReporterErrorApi($e, 'SERVER', '');
            return false;
        }

    }

    /**
     * Checks the database connection and returns a boolean value indicating if the connection is successful.
     *
     * @return bool Returns true if the database connection is successful, otherwise false.
     * @throws Exception If there is an error connecting to the database.
     */
    public function testDatabaseConnection(): bool
    {
        try {
            $connectionName = config('database.default') ?? null;
            DB::connection($connectionName)->getPdo();
            return true;
        } catch (Exception $e) {
            Log::channel($this->channel)->critical(' Database Connection Error: ' . $e);
            $this->emailReporterErrorApi($e, 'DATABASE', '');
            return false;
        }
    }

    /**
     * Test the SMTP connection.
     *
     * @return bool
     * @throws Exception
     */
    public function testSmtpConnection(): bool
    {
        try {
            $transport = new EsmtpTransport(config('mail.mailers.smtp.host'), config('mail.mailers.smtp.port'));
            $transport->setUsername(config('mail.mailers.smtp.username'));
            $transport->setPassword(config('mail.mailers.smtp.password'));
            $transport->start();
            return true;
        } catch (Exception $e) {
            Log::channel($this->channel)->critical('Mail Connection Error: ' . $e);
            return false;
        }
    }

    /**
     * Test the Redis connection.
     * install redis library predis/predis before testing
     *
     * @return bool
     */
    public function testRedisConnection(): bool
    {
        try {
            $connectionName = 'default';
            Redis::connection($connectionName)->ping();
            return true;
        } catch (Exception $e) {
            Log::channel($this->channel)->critical(' Database Connection Error: ' . $e);
            $this->emailReporterErrorApi($e, 'DATABASE', '');
            return false;
        }
    }


    /**
     * Test the AWS connection.
     *
     * @return bool
     */
    public function testAwsConnection(): bool
    {

        try {
            $connection = new StsClient([
                'region' => config('services.aws.region'),
                'version' => 'latest',
                'credentials' => array(
                    'key' => config('services.aws.key'),
                    'secret' => config('services.aws.secret')
                )
            ]);
            $connection->getCallerIdentity();
            return true;
        } catch (Exception $e) {
            Log::channel($this->channel)->critical('Aws Connection Error: ' . $e);
            $this->emailReporterErrorApi($e, 'AWS', '');
            return false;
        }

    }

    /**
     * Tests the connection to AWS S3.
     *
     * @return bool Returns true if the connection is successful, false otherwise.
     */
    public function testAwsS3Connection(): bool
    {
        try {
            Storage::disk('s3Public')->allDirectories();
            return true;
        } catch (Exception $e) {
            Log::channel($this->channel)->critical('Aws S3 Connection Error: ' . $e);
            $this->emailReporterErrorApi($e, 'AWS S3', '');
            return false;
        }
    }

    /**
     * Test the AWS SQS connection.
     *
     * @return bool Returns true if the connection is successful, false otherwise.
     * @throws Exception If there is an error connecting to AWS SQS.
     */
    public function testAwsSqsConnection(): bool
    {
        try {
            $client = new SqsClient(
                array(
                    'credentials' => array(
                        'key' => config('services.aws.key'), //use your AWS key here
                        'secret' => config('services.aws.secret') //use your AWS secret here
                    ),

                    'region' => config('services.aws.region'), //replace it with your region
                    'version' => 'latest'
                ));
            $QUEUE_URL = config('services.sqs.prefix') . '/' . config('services.sqs.queue');

            $client->sendMessage(array(
                'QueueUrl' => $QUEUE_URL, //your queue url goes here
                'MessageBody' => 'TEST SQS MESSAGE',
            ));

            $client->receiveMessage([
                'QueueUrl' => $QUEUE_URL,
                'WaitTimeSeconds' => 0,
                'VisibilityTimeout' => 0,
                'MaxNumberOfMessages' => 1
            ]);

            return true;
        } catch (Exception $e) {
            Log::channel($this->channel)->critical('Aws SQS Connection Error: ' . $e);
            $this->emailReporterErrorApi($e, 'AWS SQS', '');
            return false;
        }
    }

    /**
     * Test the connection for a given connection name.
     *
     * @param string $connectionName The name of the connection to test.
     * @throws Exception Description of the exception.
     * @return bool True if the connection is working, false otherwise.
     */
    public function testConnection($connectionName)
    {
        $testResult = $this->{"test{$connectionName}Connection"}();

        if ($testResult) {
            $this->info("Connection to {$connectionName} is working");
            return true;
        } else {
            $this->error("The Connection to the {$connectionName} is not working");
            return false;
        }
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("-- API HEALTH CHECK --");
        try {
            $this->testConnection("Server");
            $this->testConnection("Database");
            $this->testConnection("Smtp");

            return CommandAlias::SUCCESS;
        } catch (Exception $e) {
            Log::channel($this->channel)->error('Health Check Error: ' . $e);
            $this->info("This  not Worked");
            return CommandAlias::FAILURE;
        }

    }


}
