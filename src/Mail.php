<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth;

use Dspbee\Bundle\Common\Bag\GetBag;
use Dspbee\Bundle\Common\Bag\PostBag;

/**
 * Class Mail
 * @package Dspbee\Auth
 */
abstract class Mail
{
    /**
     * Mail constructor.
     * @param string $emailFrom
     * @param string $emailTo
     */
    public function __construct($emailFrom, $emailTo = '')
    {
        $this->emailFrom = $emailFrom;
        if (empty($emailTo)) {
            $get = new GetBag();
            if ($get->has('email')) {
                $emailTo = $get->fetch('email');
            } else {
                $post = new PostBag();
                if ($post->has('email')) {
                    $emailTo = $post->fetch('email');
                }
            }
        }
        $this->emailTo = $emailTo;
    }

    /**
     * @param $hash
     * @param $subject
     * @return int
     */
    abstract public function sendConfirm($hash, $subject);

    /**
     * @param $hash
     * @param $subject
     * @return int
     */
    abstract public function sendRecover($hash, $subject);

    protected $emailFrom;
    protected $emailTo;
}