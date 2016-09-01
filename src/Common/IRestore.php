<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth\Common;

/**
 * Interface IRestore
 * @package Dspbee\Auth
 */
interface IRestore
{
    /**
     * @param string $email
     * @return string
     */
    public function getHash($email = '');

    /**
     * @param string $hash
     * @return bool
     */
    public function validateHash($hash);

    /**
     * @param string $hash
     * @param string $password
     * @return bool
     */
    public function changePassword($hash, $password);
}