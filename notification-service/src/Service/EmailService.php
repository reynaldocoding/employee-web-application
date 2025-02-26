<?php

namespace App\Service;

use App\Message\SendWelcomeEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {}

    public function sendWelcomeEmail(SendWelcomeEmailMessage $messageData): void
    {
        try {
            $toEmail = $messageData->getEmail();
            $employeeName = $messageData->getName();
            $this->logger->info('EmailService - Send Welcome Email (EMAIL): ' . $toEmail);
            $this->logger->info('EmailService - Send Welcome Email (NAME): ' . $employeeName);
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
            $this->logger->info('EmailService - Send Welcome Email - Enviado correctamente');
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('EmailService - Send Welcome Email - ' . "TransportExceptionInterface: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error('EmailService - Send Welcome Email - ' . "Exception: " . $e->getMessage());
        }
    }
}
