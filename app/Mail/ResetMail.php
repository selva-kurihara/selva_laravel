<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetMail extends Mailable
{
  use Queueable, SerializesModels;

  public $member;
  public $authCode;

  public function __construct($member, $authCode)
  {
    $this->member = $member;
    $this->authCode = $authCode;
  }

  public function build()
  {
    return $this->subject('メールアドレス変更認証コード')
      ->view('emails.email-reset')
      ->with([
        'authCode' => $this->authCode,
        'member' => $this->member,
      ]);
  }
}
