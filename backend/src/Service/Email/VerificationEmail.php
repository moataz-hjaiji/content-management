<?php

namespace App\Service\Email;


use Symfony\Component\Mailer\MailerInterface;

class VerificationEmail extends SendEmail
{
    public function __construct(MailerInterface $mailer)
    {
        parent::__construct($mailer);
    }
    public function sendEmail(string $to, $template, string $subject= "Verification Email"): void
    {
        parent::sendEmail($to, $template, "Verification Email");
    }

}

