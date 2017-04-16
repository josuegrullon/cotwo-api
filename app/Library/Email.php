<?php namespace App\library;

use Mailgun\Mailgun;

class Email extends Mailgun {
	
	public static function send ($to, $subject, $message) {
		// print_r([$to, $subject, $message]); die();
		$mgClient = new Mailgun('key-4ada49d46136d92dc35c2d4d991ebfcf');
		$domain = "sandbox339c48dafb68448f9e7c7f79386b2e0c.mailgun.org";

		# Make the call to the client.
		return $mgClient->sendMessage("$domain", [
          	'from'    => 'Mailgun Sandbox <postmaster@sandbox339c48dafb68448f9e7c7f79386b2e0c.mailgun.org>',
            'to'      => $to,
			'subject' => $subject,
            'text'    => $message
        ]);
	}
}