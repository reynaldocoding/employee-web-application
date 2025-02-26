<?php

namespace App\MessageHandler;

use App\Message\SendWelcomeEmailMessage;
use App\Service\EmailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendWelcomeEmailMessageHandler
{
    public function __construct(
        private EmailService $emailService
    ) {}

    public function __invoke(SendWelcomeEmailMessage $message): void
    {
        $this->emailService->sendWelcomeEmail($message);
    }
}
