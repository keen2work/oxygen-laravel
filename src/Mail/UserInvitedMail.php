<?php

namespace EMedia\Oxygen\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvitedMail extends Mailable implements ShouldQueue
{

	protected array $data;

	use Queueable, SerializesModels;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function build()
	{
		$this->view('oxygen::emails.invitations.invitation_group')
			 ->with($this->data);

		return $this->subject($this->data['subject'] ?? 'User Invite');
	}

}
