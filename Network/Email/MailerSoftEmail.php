<?php

App::uses('CakeEmail', 'Network/Email');

class MailerLoopEmail extends CakeEmail
{

    public function __construct( $config )
    {
        parent::__construct( $config );

        $this->from('mailerloop@mailerloop.com');
    }

    public function template( $id = null )
    {

        if ( $id === null && !empty( $this->_headers['id'] ) ) {
            return $this->_headers['id'];
        }

        $this->setHeaders( array('id' => $id ) );
    }

    public function language( $language = null )
    {

        if ( $language === null && !empty( $this->_headers['language'] ) ) {
            return $this->_headers['language'];
        }

        $this->setHeaders( array('language' => $language ) );
    }
}