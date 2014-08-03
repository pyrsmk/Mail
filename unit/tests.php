<?php

########################################################### Prepare

error_reporting(E_ALL ^ E_NOTICE);

require __DIR__.'/../src/Mail.php';
require __DIR__.'/vendor/autoload.php';

$minisuite=new MiniSuite\Http('Mail');

########################################################### Base test functions

function mustThrowAnException($minisuite,$options)
{
    $mail=new Mail($options);
    try {
        $mail->send();

        return false;
    } catch (\Exception $e) {
        return true;
    }
}

function mustNotThrowAnException($minisuite,$options)
{
    $mail=new Mail($options);
    try {
        $mail->send($options);

        return true;
    } catch (\Exception $e) {
        $minisuite->error($e->getMessage());

        return false;
    }
}

########################################################### Tests : base

$minisuite->group('Undefined needed options',function ($minisuite) {

    $minisuite->test('From',function ($minisuite) {
        return mustThrowAnException($minisuite,array(
            'from' => null,
            'to' => 'unit-testing@dreamysource.fr',
            'subject' => 'test',
            'body' => 'test'
        ));
    });

    $minisuite->test('To',function ($minisuite) {
        return mustThrowAnException($minisuite,array(
            'from' => 'unit-testing@mail.php',
            'to' => null,
            'subject' => 'test',
            'body' => 'test'
        ));
    });

    $minisuite->test('Subject',function ($minisuite) {
        return mustThrowAnException($minisuite,array(
            'from' => 'unit-testing@mail.php',
            'to' => 'unit-testing@dreamysource.fr',
            'subject' => null,
            'body' => 'test'
        ));
    });

    $minisuite->test('Body',function ($minisuite) {
        return mustThrowAnException($minisuite,array(
            'from' => 'unit-testing@mail.php',
            'to' => 'unit-testing@dreamysource.fr',
            'subject' => 'test',
            'body' => null
        ));
    });

});

########################################################### Tests : contents

$minisuite->group('Contents',function ($minisuite) {

    $minisuite->test('Body',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
            'from' => 'unit-testing@mail.php',
            'to' => 'unit-testing@dreamysource.fr',
            'subject' => 'Simple body',
            'body' => 'A simple body should be displayed'
        ));
    });

    $minisuite->test('Html',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
            'from' => 'unit-testing@mail.php',
            'to' => 'unit-testing@dreamysource.fr',
            'subject' => 'HTML contents',
            'body' => null,
            'html' => '<h1>HTML contents should be displayed</h1>'
        ));
    });

    $minisuite->test('Both',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
            'from' => 'unit-testing@mail.php',
            'to' => 'unit-testing@dreamysource.fr',
            'subject' => 'Both simple body and HTML contents',
            'body' => 'Simple body and HTML contents should be displayed',
            'html' => '<h1>Simple body and HTML contents should be displayed</h1>'
        ));
    });

});

########################################################### Tests : various

$minisuite->test('Contacts',function ($minisuite) {
    return mustNotThrowAnException($minisuite,array(
        'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
        'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
        'subject' => 'Contacts',
        'body' => "From field should display : Mail Unit Testing <unit-testing@mail.php>\nRecipient field should display : Unit Testing Recipient <unit-testing@dreamysource.fr>"
    ));
});

$minisuite->test('Sender',function ($minisuite) {
    return mustNotThrowAnException($minisuite,array(
        'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
        'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
        'sender' => 'unit-testing@mail.php',
        'subject' => 'Sender',
        'body' => "The sender should be : Sender <sender@mail.php>"
    ));
});

$minisuite->test('Reply-to',function ($minisuite) {
    return mustNotThrowAnException($minisuite,array(
        'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
        'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
        'replyto' => 'reply-to@mail.php',
        'subject' => 'Reply-to',
        'body' => "The reply-to field should be : Reply-To <reply-to@mail.php>"
    ));
});

########################################################### Tests : multiple recipients

$minisuite->group('Multiple recipients',function ($minisuite) {

    $minisuite->test('To',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
            'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
            'to' => array(
                'Unit Testing Recipient' => 'unit-testing@dreamysource.fr',
                'unit-testing2@dreamysource.fr',
                'Unit Testing Recipient 3' => 'unit-testing3@dreamysource.fr'
            ),
            'subject' => 'Multiple TO recipients',
            'body' => "Should be received by Unit Testing Recipient <unit-testing@dreamysource.fr>, unit-testing2@dreamysource.fr and Unit Testing Recipient 3 <unit-testing3@dreamysource.fr>"
        ));
    });

    $minisuite->test('Cc',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
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
    });

    $minisuite->test('Bcc',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
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
    });

});

########################################################### Tests : attachments

$minisuite->group('Attachments',function ($minisuite) {

    $minisuite->test('With body',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
            'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
            'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
            'subject' => 'Attachments',
            'body' => "Should have one attachment",
            'attachments' => array('attachments/_temple_tree__by_azenoire-d6mk8ik.jpg')
        ));
    });

    $minisuite->test('With HTML',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
            'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
            'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
            'subject' => 'Attachments',
            'html' => "<h1>Should have one attachment</h1>",
            'attachments' => array('attachments/_temple_tree__by_azenoire-d6mk8ik.jpg')
        ));
    });

    $minisuite->test('With both',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
            'from' => array('Mail Unit Testing'=>'unit-testing@mail.php'),
            'to' => array('Unit Testing Recipient'=>'unit-testing@dreamysource.fr'),
            'subject' => 'Attachments',
            'body' => "Should have one attachment",
            'html' => "<h1>Should have one attachment</h1>",
            'attachments' => array('attachments/_temple_tree__by_azenoire-d6mk8ik.jpg')
        ));
    });

    $minisuite->test('Several attachments',function ($minisuite) {
        return mustNotThrowAnException($minisuite,array(
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
    });

});

########################################################### Run tests

$minisuite->run();
