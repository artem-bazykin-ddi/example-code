<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\Interfaces\StoreMagicTokenInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuthEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $magicToken;

    /**
     * AuthEmail constructor.
     *
     * @param StoreMagicTokenInterface $storeMagicToken
     */
    public function __construct(StoreMagicTokenInterface $storeMagicToken)
    {
        $this->magicToken = $storeMagicToken->getMagicToken();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this
            ->subject('Complete your Virtual Coach login')
            ->view('auth.email')
            ->with(['link' => $this->getVerifyLink()]);
    }

    /**
     * @return string
     */
    protected function getVerifyLink(): string
    {
        return route('open-app', ['token' => $this->magicToken]);
    }
}
