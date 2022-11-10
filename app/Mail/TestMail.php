<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (\Schema::hasTable('email_setting')) {
            $settings = DB::table('email_setting')->first();
            if ($settings) //checking if table is not empty
            {
                return $this->from($settings->send_email_from)
                ->subject('Testing email')
                ->view('test_email_template')
                ->with('data', $this->data);
            }
        }

    }
}
