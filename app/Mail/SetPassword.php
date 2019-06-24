<?php

namespace App\Mail;

use App\PasswordReset;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class SetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $worker;
    protected $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Worker $worker, $token)
    {
        $this->worker = $worker;
        $this->token = $token;

        PasswordReset::updateOrCreate(['email' => $worker->email, 'token' => $token,'created_at' => Carbon::now()->toDateTimeString()]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['address' => env('MAIL_USERNAME'),'name' => 'Grupo20 DAD'])
            ->view('mail.setpassword')
            ->with(['worker' => $this->worker, 'token' => $this->token]);
    }
}
