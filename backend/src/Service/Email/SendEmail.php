<?php

namespace App\Service\Email;

use Mailtrap\AbstractMailtrapClient;
use Mailtrap\Config;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendEmail
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {
    }



    /**
     * @throws TransportExceptionInterface
     */
     public function sendEmail(string $to,$template,string $subject)
    {
        $email = (new Email())
            ->from('moatazhjaiji@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($template)
            ;
            $this->mailer->send($email);
    }

}
