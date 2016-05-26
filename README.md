Mail 0.6.8
==========

Mail is a small library that aims to provide a light and simple mail implementation.

Installing
----------

Pick up the source or install it with [Composer](https://getcomposer.org/) :

```
composer require pyrsmk/mail
```

Sending a basic e-mail
----------------------

Mail is built on top of Chernozem to have an ease to use configuration.

```php
$mail = new Mail;
$mail['from'] = 'lionel@example.org';
$mail['to'] = 'peter@another-site.org';
$mail['subject'] = 'Hey, I saw you last night!';
$mail['body'] = 'I saw you. Or not.';
$mail->send();
```

And that's all if you want to send a basic e-mail! Pretty simple, isn't it?

But we have a problem. Our address will be shown as `from@example.org` and that is really ugly. Let's correct this :

```php
$mail['from'] = array('Lionel McGonagall'=>'lionel@example.org');
```

It will be shown like this : `Lionel McGonagall <from@example.org>`.

Sending to more than one recipient works the same way :

```php
$mail['to'] = array(
    'Peter' => 'peter@another-site.org',
    'Anaïs' => 'anais@wonderful.net',
    'Grandma' => 'iam@old.com'
);
```

That syntax works for `cc` and `bcc` parameters too. Note that `sender` and `replyto` are accessible too but only with the simple `email` syntax :

```php
$mail['sender'] = 'peter@another-site.org';
$mail['replyto'] = 'anais@wonderful.net';
```

Sending a more complex e-mail
-----------------------------

Let's say we want to send a billing e-mail to an user from our site with an eye-candy design in HTML, a text fallback and a PDF file attachment. That's how we can proceed :

```php
$mail['html'] = '<h1>An awesome title for an awesome bill</h1>';
$mail['body'] = 'Your mail client does not support HTML rendering. So bad.';
$mail['attachments'] = array(__DIR__.'/my_generated_bill.pdf');
```

That's all we need as configuration ;)

You can also define a filename for an attachment file : 

```php
$mail['attachments'] = array(
	$_FILES['image1']['name'] => $_FILES['image1']['tmp_name'],
	$_FILES['image2']['name'] => $_FILES['image2']['tmp_name']
);
```

E-mail validation
-----------------

Additionally, you can validate an e-mail address :

```php
if(Mail::validate('lionel@example.org')){
    echo 'The e-mail address is valid';
}
else{
    echo 'The e-mail address is NOT valid';
}
```

License
-------

[MIT](http://dreamysource.mit-license.org) in your face.
