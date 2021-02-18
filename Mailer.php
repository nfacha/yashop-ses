<?php
/**
 *
 * Mailer implements a mailer based on Amazon SES.
 *
 * To use Mailer, you should configure it in the application configuration like the following,
 *
 * ~~~
 * 'components' => [
 *     ...
 *     'mail' => [
 *         'class' => 'nfacha\ses\Mailer',
 *         'access_key' => 'Your access key',
 *         'secret_key' => 'Your secret key'
 *     ],
 *     ...
 * ],
 * ~~~
 *
 * To send an email, you may use the following code:
 *
 * ~~~
 * Yii::$app->mail->compose('contact/html', ['contactForm' => $form])
 *     ->setFrom('from@domain.com')
 *     ->setTo($form->email)
 *     ->setSubject($form->subject)
 *     ->send();
 * ~~~
 *
 * @author Vitaliy Ofat <ofatv22@gmail.com>
 */

namespace nfacha\ses;

use nfacha\ses\libs\SimpleEmailService;
use Yii;
use yii\mail\BaseMailer;

class Mailer extends BaseMailer
{
    /**
     * @var string message default class name.
     */
    public $messageClass = 'nfacha\ses\Message';

    /**
     * @var string Amazon ses api access key
     */
    public $access_key;

    /**
     * @var string Amazon ses api secret key
     */
    public $secret_key;

    /**
     * @var string A default from address to send email
     */
    public $default_from;

    /**
     * @var string Amazon ses host
     */
    public $host = 'email.us-east-1.amazonaws.com';

    /**
     * @var \nfacha\ses\libs\SimpleEmailService SimpleEmailService instance.
     */
    private $_ses;

    private $_viewPath;

    /**
     * @return \nfacha\ses\libs\SimpleEmailService SimpleEmailService instance.
     */
    public function getSES()
    {
        if (!is_object($this->_ses)) {
            $this->_ses = new SimpleEmailService($this->access_key, $this->secret_key, $this->host);
        }

        return $this->_ses;
    }

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        $address = $message->getTo();
        if (is_array($address)) {
            $address = implode(', ', array_keys($address));
        }
        Yii::info('Sending email "' . $message->getSubject() . '" to "' . $address . '"', __METHOD__);

        if ( is_null($message->getFrom()) && isset($this->default_from)) {
            if(!is_array($this->default_from)){
                $this->default_from = array($this->default_from => $this->default_from);
            }
            $message->setFrom($this->default_from);
        }
        $res = $this->getSES()->sendEmail($message->getSesMessage());

        $message->setDate(time());

        return count($res) > 0;
    }

	/**
	 * @return string the directory that contains the view files for composing mail messages
	 * Defaults to '@app/mail'.
	 */
	public function getViewPath()
	{
		if ($this->_viewPath === null) {
			$this->setViewPath('@app/mail');
		}

		return $this->_viewPath;
	}

	/**
	 * @param string $path the directory that contains the view files for composing mail messages
	 * This can be specified as an absolute path or a [path alias](guide:concept-aliases).
	 */
	public function setViewPath($path)
	{
		$this->_viewPath = Yii::getAlias($path);
	}
}
