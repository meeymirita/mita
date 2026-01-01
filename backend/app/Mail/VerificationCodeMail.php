<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User   $user,
        public string $code
    ){}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Код подтверждения email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
            with: [
                'user' => $this->user,
                'code' => $this->code,
                'frontend_url' => 'https://meeymirita.ru/',
                'himary_url' => $this->getImageToEmail('sakura.png'),
                'sakura_url' => $this->getImageToEmail('sakura.png'),
            ]
        );
    }

    /**
     * @return string
     */
    private function determineBaseUrl(): string
    {
        return request()->getSchemeAndHttpHost();
    }

    /**
     * @param string $image
     * @return string
     */
    public function getImageToEmail(string $image): string
    {
        return $this->determineBaseUrl() . "/storage/images/image_to_email/{$image}";
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
