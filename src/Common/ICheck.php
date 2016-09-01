<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth\Common;

/**
 * Interface ICheck
 * @package Dspbee\Auth\Common
 */
interface ICheck
{
    /**
     * Return true if email already registered.
     * 
     * @return bool
     */
    public function isRegistered();
}