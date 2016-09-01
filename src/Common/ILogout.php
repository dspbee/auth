<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth\Common;

/**
 * Interface ILogout
 * @package Dspbee\Auth\Common
 */
interface ILogout
{
    /**
     * @param $hash
     * @return bool
     */
    public function quit($hash = '');
}