<?php

namespace SwiftDevLabs\WaveApps;

use Dotenv\Dotenv;
use League\CLImate\CLImate;
use PHPMailer\PHPMailer\PHPMailer;

class ReceiptUpload
{
    private $allowedTypes = [
        'application/pdf',
        'image/jpeg',
        'image/tiff',
        'image/bmp',
        'image/png',

    ];

    private $options = [
        'debug' => false,
    ];

    private $fileName = '';

    private $cli = null;

    public function __construct($fileName, array $options = [])
    {
        $this->cli = new CLImate();

        // Checks for enviroment variables
        $dotenv = new Dotenv(realpath(__DIR__ . '/../../../'));
        $dotenv->load();

        $this->options = array_merge($this->options, $options);

        try
        {
            $dotenv->required([
                'SMTP_HOST',
            ]);
        }
        catch (\Exception $e)
        {
            $this->cli->error($e->getMessage());

            exit(1);
        }

        $this->setFileName($fileName);
    }

    public function run()
    {
        if (! $this->options['debug'])
        {
            $confirmed = $this->cli->confirm('Continue to email receipts to receipts@waveapps.com?')->confirmed();
        }
        else
        {
            $confirmed = true;
        }

        if ($confirmed)
        {
            if (is_dir($this->getFileName()))
            {
                /**
                 * Find files and process
                 */

                foreach (scandir($this->getFileName()) as $target)
                {
                    $target = $this->getFileName() . '/' . $target;
                    if (in_array(mime_content_type($target), $this->getAllowedTypes()))
                    {
                        $this->processFile(realpath($target));
                    }
                }
            }
            else
            {
                $this->processFile($this->getFileName());
            }
        }
        else
        {
            $this->cli->output('Ok. Exiting...');
        }
    }

    function processFile($fileName)
    {
        if ($this->options['debug'])
        {
            $this->cli->output("{$fileName} will be uploaded");
        }
        else
        {
            $this->cli->inline("Processing {$fileName}... ");

            $mail = new PHPMailer();
            $mail->isSMTP();

            $mail->SMTPAuth = true;
            $mail->Host = getenv('SMTP_HOST');

            if ($smtp_port = getenv('SMTP_PORT'))
            {
                $mail->Port = $smtp_port;
            }

            if ($smtp_username = getenv('SMTP_USERNAME'))
            {
                $mail->Username = $smtp_username;
            }

            if ($smtp_password = getenv('SMTP_PASSWORD'))
            {
                $mail->Password = $smtp_password;
            }

            // Starts sending the email
            if ($smtp_email = getenv('SMTP_EMAIL'))
            {
                $mail->setFrom($smtp_email);
            }

            $mail->addAddress('receipts@waveapps.com');

            $mail->Subject = 'Receipt sent on ' . date('Y-m-d H:i:s');
            $mail->Body    = 'Attached is the receipt for processing';

            $mail->addAttachment($fileName);
            
            if ($mail->send())
            {
                $this->cli->green('Email sent!');
            }
            else
            {
                $this->cli->error('Error sending email');
            }
        }
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setAllowedTypes(array $allowedTypes)
    {
        $this->allowedTypes = $allowedTypes;

        return $this;
    }

    public function getAllowedTypes()
    {
        return $this->allowedTypes;
    }
}