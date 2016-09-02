<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth\Common;

use Dspbee\Auth\Bundle\LoginInput;

/**
 * Interface ILogin
 * @package Dspbee\Auth\Common
 */
interface ILogin
{
    /**
     * @param LoginInput $input
     * @return int
     */
    public function enter(LoginInput $input);

    /**
     * @return int
     */
    public function userId();

    /**
     * @return string
     */
    public function hash();

    /**
     * @return string
     */
    public function error();
}