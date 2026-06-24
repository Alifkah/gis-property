<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class BrevoApiTransport extends AbstractTransport
{
    public function __construct(private string $key)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $originalMessage = $message->getOriginalMessage();

        if (! $originalMessage instanceof Email) {
            return;
        }

        // Extract sender
        $from = $originalMessage->getFrom()[0] ?? null;
        $fromAddress = $from ? $from->getAddress() : config('mail.from.address');
        $fromName = $from ? $from->getName() : config('mail.from.name');

        // Extract recipients
        $to = [];
        foreach ($originalMessage->getTo() as $address) {
            $to[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: null,
            ];
        }

        // Extract subject & body
        $subject = $originalMessage->getSubject();
        $html = $originalMessage->getHtmlBody();
        $text = $originalMessage->getTextBody();

        $payload = [
            'sender' => [
                'email' => $fromAddress,
                'name' => $fromName ?: 'Sender',
            ],
            'to' => $to,
            'subject' => $subject,
        ];

        if ($html) {
            $payload['htmlContent'] = $html;
        }

        if ($text) {
            $payload['textContent'] = $text;
        }

        $response = Http::withHeaders([
            'api-key' => $this->key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if (! $response->successful()) {
            throw new \Exception('Brevo API send failed: '.$response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
