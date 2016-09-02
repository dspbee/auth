<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth\Common;

use Dspbee\Auth\Bundle\RegistrationInput;

/**
 * Interface IRegistration
 * @package Dspbee\Auth
 */
interface IRegistration
{
    /**
     * @param RegistrationInput $input
     * @return string - registration hash, if empty then some error occurs
     */
    public function register(RegistrationInput $input);

    /**
     * @param string $hash
     * @return bool
     */
    public function confirmRegistration($hash);
    
    /**
     * @return string
     */
    public function error();
}