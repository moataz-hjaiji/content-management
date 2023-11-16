<?php

namespace App\Service;


use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private MailtrapClient $mailtrapClient
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function VerificationEmail(string $email)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('MAILTRAP_API_KEY'));

        $config = Configuration::getDefaultConfiguration()->setApiKey('partner-key', getenv('MAILTRAP_API_KEY'));


        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );
        $sendEmail = new SendSmtpEmail([
            'subject'=>'verif email',
            'sender'=>'test@test.com',
            'to'=>$email,
            'htmlContent'=>'<body><h1>verification email</h1></body>'
        ]);

//        to($email)
//            ->from('confirmation@test.com')
//            ->htmlTemplate('email/confirmation_email.html.twig')
//            ->subject('verification email')
//            ->text('Sending email')
//            ;
        try {
            //$response = $this->mailtrapClient->sandbox()->emails()->send($sendEmail, 1000001); // Required second param -> inbox_id
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            dd(ResponseHelper::toArray($result));
        }catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }
}
