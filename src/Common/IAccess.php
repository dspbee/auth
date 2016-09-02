<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth\Common;

use Dspbee\Auth\User;

/**
 * Interface IAccess
 * @package Dspbee\Auth\Common
 */
interface IAccess
{
    /**
     * Return User. Can check access rights for route and method.
     * If user not verified or access not allowed then User id is empty.
     * @param string $hash
     * @param string $route
     * @param string $method
     * @param bool $default
     * @return User
     */
    public function getUser($hash = '', $route = '', $method = '*', $default = false): User;
}