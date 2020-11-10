<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackEmail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var Feedback
     */
    private $feedback;

    /**
     * Create a new message instance.
     *
     * @param Feedback $feedback
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this
            ->subject('New user feedback')
            ->view('email.feedback')
            ->with([
                'userName' => $this->feedback->user->name,
                'userEmail' => $this->feedback->user->email,
                'clientId' => $this->feedback->user->client->id,
                'text' => $this->feedback->text,
            ])
            ->to(env('MAIL_SUPPORT', 'support@leadinclusively.com'));
    }
}
