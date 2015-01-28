<?php

########################################################### Prepare

error_reporting(E_ALL);

require __DIR__.'/../src/Mail.php';
require __DIR__.'/vendor/autoload.php';

$minisuite=new MiniSuite('Mail');

########################################################### Base test functions

function send($options){
	$mail=new Mail($options);
	try{
		$mail->send();
		return true;
	}
	catch(\Exception $e){
		return false;
	}
}

########################################################### Tests : base

$minisuite->group('Undefined needed options',function($minisuite){

	$val=send(array(
		'from' => null,
		'to' => 'unit-testing@dreamysource.fr',
		'subject' => 'test',
		'body' => 'test'
	));

	$minisuite->expects('From')
			  ->that($val)
			  ->equals(false);

	$val=send(array(
		'from' => 'unit-testing@mail.php',
		'to' => null,
		'subject' => 'test',
		'body' => 'test'
	));
	
	$minisuite->expects('To')
			  ->that($val)
			  ->equals(false);

	$val=send(array(
		'from' => 'unit-testing@mail.php',
		'to' => 'unit-testing@dreamysource.fr',
		'subject' => null,
		'body' => 'test'
	));
	
	$minisuite->expects('Subject')
			  ->that($val)
			  ->equals(false);

	$val=send(array(
		'from' => 'unit-testing@mail.php',
		'to' => 'unit-testing@dreamysource.fr',
		'subject' => 'test',
		'body' => null
	));
	
	$minisuite->expects('Body')
			  ->that($val)
			  ->equals(false);

});

########################################################### Tests : contents

$minisuite->group('Contents',function($minisuite){

	$val=send(array(
		'from' => 'unit-testing@mail.php',
		'to' => 'unit-testing@dreamysource.fr',
		'subject' => 'Simple body',
		'body' => 'A simple body should be displayed'
	));
	
	$minisuite->expects('Body')
			  ->that($val)
			  ->equals(true);

	$val=send(array(
		'from' => 'unit-testing@mail.php',
		'to' => 'unit-testing@dreamysource.fr',
		'subject' => 'HTML contents',
		'body' => null,
		'html' => '<h1>HTML contents should be displayed</h1>'
	));
	
	$minisuite->expects('Html')
			  ->that($val)
			  ->equals(true);

	$val=send(array(
		'from' => 'unit-testing@mail.php',
		'to' => 'unit-testing@dreamysource.fr',
		'subject' => 'Both simple body and HTML contents',
		'body' => 'Simple body and HTML contents should be displayed',
		'html' => '<h1>Simple body and HTML contents should be displayed</h1>'
	));
	
	$minisuite->expects('Both')
			  ->that($val)
			  ->equals(true);

});

########################################################### Tests : various

$val=send(array(
	'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
	'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
	'subject' => 'Contacts',
	'body' => "From field should display : Mail Unit Testing <unit-testing@mail.php>\nRecipient field should display : Unit Testing Recipient <unit-testing@dreamysource.fr>"
));

$minisuite->expects('Contacts')
		  ->that($val)
		  ->equals(true);

$val=send(array(
	'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
	'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
	'sender' => 'unit-testing@mail.php',
	'subject' => 'Sender',
	'body' => "The sender should be : Sender <sender@mail.php>"
));

$minisuite->expects('Sender')
		  ->that($val)
		  ->equals(true);

$val=send(array(
	'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
	'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
	'replyto' => 'reply-to@mail.php',
	'subject' => 'Reply-to',
	'body' => "The reply-to field should be : Reply-To <reply-to@mail.php>"
));

$minisuite->expects('Reply-to')
		  ->that($val)
		  ->equals(true);

########################################################### Tests : multiple recipients

$minisuite->group('Multiple recipients',function($minisuite){

	$val=send(array(
		'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
		'to' => array(
			'Unit Testing Recipient' => 'unit-testing@dreamysource.fr',
			'unit-testing2@dreamysource.fr',
			'Unit Testing Recipient 3' => 'unit-testing3@dreamysource.fr'
		),
		'subject' => 'Multiple TO recipients',
		'body' => "Should be received by Unit Testing Recipient <unit-testing@dreamysource.fr>, unit-testing2@dreamysource.fr and Unit Testing Recipient 3 <unit-testing3@dreamysource.fr>"
	));
	
	$minisuite->expects('To')
			  ->that($val)
			  ->equals(true);

	$val=send(array(
		'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
		'cc' => array(
			'Unit Testing Recipient' => 'unit-testing@dreamysource.fr',
			'unit-testing2@dreamysource.fr',
			'Unit Testing Recipient 3' => 'unit-testing3@dreamysource.fr'
		),
		'to' => 'unit-testing4@dreamysource.fr',
		'subject' => 'CC',
		'body' => "Should be received by Unit Testing Recipient <unit-testing@dreamysource.fr>, unit-testing2@dreamysource.fr, Unit Testing Recipient 3 <unit-testing3@dreamysource.fr> and unit-testing4@dreamysource.fr in Carbon Copy"
	));
	
	$minisuite->expects('Cc')
			  ->that($val)
			  ->equals(true);

	$val=send(array(
		'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
		'bcc' => array(
			'Unit Testing Recipient' => 'unit-testing@dreamysource.fr',
			'unit-testing2@dreamysource.fr',
			'Unit Testing Recipient 3' => 'unit-testing3@dreamysource.fr'
		),
		'to' => 'unit-testing4@dreamysource.fr',
		'subject' => 'BCC',
		'body' => "Should be received by Unit Testing Recipient <unit-testing@dreamysource.fr>, unit-testing2@dreamysource.fr, Unit Testing Recipient 3 <unit-testing3@dreamysource.fr> and unit-testing4@dreamysource.fr in Blind Carbon Copy"
	));
	
	$minisuite->expects('Bcc')
			  ->that($val)
			  ->equals(true);

});

########################################################### Tests : attachments

$minisuite->group('Attachments',function($minisuite){

	$val=send(array(
		'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
		'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
		'subject' => 'Attachments',
		'body' => "Should have one attachment",
		'attachments' => array('attachments/_temple_tree__by_azenoire-d6mk8ik.jpg')
	));
	
	$minisuite->expects('With body')
			  ->that($val)
			  ->equals(true);

	$val=send(array(
		'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
		'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
		'subject' => 'Attachments',
		'html' => "<h1>Should have one attachment</h1>",
		'attachments' => array('attachments/_temple_tree__by_azenoire-d6mk8ik.jpg')
	));
	
	$minisuite->expects('With HTML')
			  ->that($val)
			  ->equals(true);

	$val=send(array(
		'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
		'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
		'subject' => 'Attachments',
		'body' => "Should have one attachment",
		'html' => "<h1>Should have one attachment</h1>",
		'attachments' => array('attachments/_temple_tree__by_azenoire-d6mk8ik.jpg')
	));
	
	$minisuite->expects('With both')
			  ->that($val)
			  ->equals(true);

	$val=send(array(
		'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
		'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
		'subject' => 'Attachments',
		'body' => "Should have three attachments",
		'attachments' => array(
			'attachments/_temple_tree__by_azenoire-d6mk8ik.jpg',
			'attachments/lahulotte_7_regles.pdf',
			'attachments/CGV.txt'
		)
	));
	
	$minisuite->expects('Several attachments')
			  ->that($val)
			  ->equals(true);

});