<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeEmail(string $toEmail, string $employeeName): void
    {
        $email = (new Email())
            ->from('no-reply@gmail.com')
            ->to($toEmail)
            ->subject('¡Bienvenido a la Empresa!')
            ->html("<html>
                        <body>
                            <h1>Hola $employeeName, te damos las bienvenida a la Empresa</h1>
                            <p>Estamos emocionados de que trabajes con nosotros.</p>
                        </body>
                    </html>");

        $this->mailer->send($email);
    }
}
