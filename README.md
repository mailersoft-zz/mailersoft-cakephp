# MailerSoft transactional email plugin for CakePHP


### Installation

You can clone the plugin right into your project:

```
cd path/to/app/Plugin or /plugins
git clone https://github.com/mailersoft/mailersoft-cakephp.git Mailersoft
```

### Configuration

Create the file app/Config/email.php with the class EmailConfig.

```php
<?php
class EmailConfig {
    public $mailersoft = array(
        'transport' => 'MailerSoft.MailerSoft',
        'api_key' => 'your-mailersoft-api-key',
    );
}
```

Make sure to modified the API key to match your MailerSoft API key. You can find it in Account&Settings » Account Details

### Usage

This plugin uses [CakeEmail](http://book.cakephp.org/2.0/en/core-utility-libraries/email.html) but there are a few differences
for sending using MailerSoft plugin. You have to pass additional header `id` that corresponds to mail ID in MailerSoft.

Sample to send a simple message:

```php
<?php
App::uses('CakeEmail', 'Network/Email');
$email = new CakeEmail();

$email->config('mailersoft');
$email->from('your@email.com');
$email->to('recipient@email.com');
$email->subject('Test Subject');
$email->addHeaders( array('id' => 1111 ) )
$email->send();
```

There is another option to send messages. You can use MailerSoftEmail instead of CakeEmail. 
It has some additional methods that will help you to write less code. 
You can setup sender email and name right in the MailerLoop so it is not required to pass it when using MailerLoopEmail.
Another advantage is you can use templateId() and language() instead of adding headers.
MailerSoftEmail class will automatically use `mailersoft` email configuration

Sample to send a simple message with MailerSoftEmail:

```php
<?php
App::uses('MailerSoftEmail', 'Plugin/MailerSoft/Network/Email');

$email = new MailerSoftEmail();
$email->to('recipient@email.com');
$email->subject('Test Subject');
$email->templateId( 1111 );
$email->send();
```

### Debugging

You can see the response from MailerSoft in the return value when you send a message:

```php
<?php
$result = $email->send();
$this->log($result, 'debug');
```

If there are any errors, they'll be included in the response. You can check MailerSoft API documentation for error code details:

https://docs.mailersoft.com/pages/messages

