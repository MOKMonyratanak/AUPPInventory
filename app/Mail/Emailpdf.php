<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Emailpdf extends Mailable
{
    use Queueable, SerializesModels;
    public $msg;
    public $subject;
    public $attachData;
    public $user;
    public $ccEmails;


    /**
     * Create a new message instance.
     */
    public function __construct($msg, $subject, $attachData, $user)
    {
        $this->msg = $msg;
        $this->subject = $subject;
        $this->attachData = $attachData;
        $this->user = $user;
        $this->ccEmails = 'itsupadmin@aupp.edu.kh';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
            cc: $this->ccEmails
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail',
            with: ['user' => $this->user]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $this->attachData, // The data source
                'Issued_Assets.pdf'               // The file name
            ),
        ];
    }
    
}
