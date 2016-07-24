<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 24/07/16
 * Time: 1:25 PM
 */

namespace PhpMQ\Utility;


class MessageBuilder
{

    const SEPARATOR = '{@-@}';
    const KEY_PART = 'part';
    const KEY_REMAIN = 'remain';

    private $buffer = '';
    private $messages = array();

    public function addData($data)
    {
        $parts = array(
            self::KEY_PART => '',
            self::KEY_REMAIN => '',
        );

        // add the remaing last buffer to the new
        // data received
        $data = $this->buffer.$data;
        $this->buffer = '';

        // we found the separator, it means that we have
        // to finish an existing message

        while ($parts = $this->getFirstPart($data)) {

            $data = $parts[self::KEY_REMAIN];
            $part = $parts[self::KEY_PART];

            if (!empty($part)) {
                $this->messages[] = $part;
            } else {
                break;
            }
        }

        $this->buffer = $data;
    }

    public function getFirstPart($data)
    {
        $firstPart = '';
        $remain = '';

        $delimiterPos = strpos($data, self::SEPARATOR);
        if ($delimiterPos !== false) {
            $firstPart = substr($data, 0, $delimiterPos);
            $remain = substr($data, $delimiterPos + strlen(self::SEPARATOR), strlen($data));
        } else {
            $remain = $data;
        }

        return array(
            self::KEY_PART => $firstPart,
            self::KEY_REMAIN => $remain,
        );
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function hasMessages()
    {
        return !empty($this->messages);
    }

    public function clearMessages()
    {
        $this->messages = array();
    }

    public function getBuffer()
    {
        return $this->buffer;
    }
}