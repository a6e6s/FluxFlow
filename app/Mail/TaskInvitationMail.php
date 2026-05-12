<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\TaskInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TaskInvitation $invitation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('invitations.mail.subject', [
                'task' => $this->invitation->task?->title ?? '',
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-invitation',
            with: [
                'invitation' => $this->invitation,
                'task' => $this->invitation->task,
                'project' => $this->invitation->task?->project,
                'inviter' => $this->invitation->inviter,
                'acceptUrl' => route('invitations.show', ['token' => $this->invitation->token]),
            ],
        );
    }
}
