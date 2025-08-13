<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
  use Queueable, SerializesModels;

  public $member;
  public $token;

  public function __construct($member, $token)
  {
    $this->member = $member;
    $this->token = $token;
  }

  public function build()
  {
    return $this->subject('パスワード再設定')
    ->view('emails.password_reset')
    ->with([
      'url' => url('password/reset/' . $this->member->email . '/' . $this->token),
      'member' => $this->member,
    ]);
  }
}

