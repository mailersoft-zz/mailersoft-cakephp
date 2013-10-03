<?php
/**
 * MailerLoop Transport
 */

App::uses('AbstractTransport', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');

/**
 * MailerLoop Transport
 *
 * This class is used for sending email messages
 * using the MailerLoop API http://www.mailerloop.com/
 *
 */
class MailerLoopTransport extends AbstractTransport {

	protected $data;
	
	private $url = 'https://test.mailerloop.com/api/v1/messages';

    /**
     * @param CakeEmail $email
     * @return array
     * @throws CakeException
     */
    public function send( CakeEmail $email ) {

        // Setup connection
        $connection = & new HttpSocket();

        $config = $email->config();

        $this->data['apiKey'] = $config['api_key'];
        $this->data['test'] = !empty( $config['test'] );

        $request = $this->buildRequest( $email );

        // Send message
        $return = $connection->post( $this->url , http_build_query( $request ) );

        // Return data
        $result = json_decode($return, true);

        return array('MailerLoop' => $result );
    }

    protected function buildRequest( CakeEmail $email )
    {

        $sender = $email->from();
        if ( key( $sender ) != 'mailerloop@mailerloop.com' ) {
            $this->data['fromEmail'] = key( $sender );
            $this->data['fromName'] = reset( $sender );
        }


        $replyTo = $email->replyTo();
        if ( !empty( $replyTo ) ) {
            $this->data['replyToEmail'] = key( $replyTo );
            $this->data['replyToName'] = reset( $replyTo );
        }

        $headers = $email->getHeaders( array('subject','id','language') );

        if ( empty( $headers['id'] ) ) {
            throw new CakeException("ID header is required");
        }

        $this->data['templateId'] = $headers['id'];

        if ( !empty( $headers['language'] ) ) {
            $this->data['language'] = $headers['language'];
        }

        $variables = $email->viewVars();
        $variables['subject'] = $headers['Subject'];

        $recipients = array_merge( $email->to(), $email->cc(), $email->bcc() );

        if ( count( $recipients ) > 1 ) {

            $this->data['batch'] = array();

            foreach ( $recipients as $recipientEmail => $recipientName ) {

                $this->data['batch'][] = array(
                    'variables' => $variables,
                    'templateId' => $headers['id'],
                    'recipientName' => $recipientName,
                    'recipientEmail' => $recipientEmail
                );

            }
        } else {

            $this->data['recipientName'] = reset( $recipients );
            $this->data['recipientEmail'] = key( $recipients );
            $this->data['variables'] = $variables;

        }

        $this->addAttachments( $email );

        return $this->data;

    }

    protected function addAttachments( CakeEmail $email )
    {

        $attachments = $email->attachments();

        $this->data['attachments'] = array();

        foreach ( $attachments as $filename => $data ) {

            $this->data['attachments'][] = array(
                'filename' => $filename,
                'content' => file_get_contents( $data['file'] )
            );

        }

    }

}
