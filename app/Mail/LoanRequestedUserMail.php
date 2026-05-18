<?php

namespace App\Mail;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanRequestedUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Número de tentativas antes de falhar definitivamente.
     */
    public int $tries = 3;

    /**
     * Segundos de espera entre tentativas (backoff progressivo).
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [5, 10];
    }

    /**
     * Create a new message instance.
     */
    public function __construct(public Loan $loan) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmação de Pedido de Requisição - ' . $this->loan->loan_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.requested-user',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
