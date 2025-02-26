<?php

namespace App\Messenger\Serializer;

use App\Message\SendWelcomeEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class SendWelcomeEmailMessageSerializer implements SerializerInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private LoggerInterface $logger
    ) {}

    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        try {
            $message = $this->serializer->deserialize($body, SendWelcomeEmailMessage::class, 'json');
        } catch (\Throwable $throwable) {
            throw new MessageDecodingFailedException($throwable->getMessage());
        }

        $stamps = [];
        if (!empty($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        return new Envelope($message, $stamps);
    }


    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();
        $stamps = $envelope->all();

        if ($message instanceof SendWelcomeEmailMessage) {
            $data = [
                'email' => $message->getEmail(),
                'name' => $message->getName(),
            ];
        } else {
            throw new \Exception(sprintf('Serializer does not support message of type %s.', $message::class));
        }

        $array = [
            'body' => json_encode($data),
            'headers' => [
                'stamps' => serialize($stamps)
            ]
        ];
        $this->logger->info('SendWelcomeEmailMessageSerializer - encode: ' . json_encode($array));
        return $array;
    }
}
