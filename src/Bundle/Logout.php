<?php
namespace Dspbee\Auth\Bundle;

use Dspbee\Auth\Common\Common;
use Dspbee\Auth\Common\ILogout;
use Dspbee\Auth\Token\Token;
use Dspbee\Bundle\Common\Bag\CookieBag;

/**
 * Class Logout
 * @package Dspbee\System\Auth\Mysql
 */
class Logout extends Common implements ILogout
{
    /**
     * @param string $hash
     * @param bool $setCookie
     * @return bool
     * @throws \ErrorException
     */
    public function quit($hash = '', $setCookie = true)
    {
        if (empty($hash)) {
            $cookie = new CookieBag();
            if ($cookie->has($this->tokenName)) {
                $hash = $cookie->fetch($this->tokenName);
            }
        }

        if (!empty($hash)) {
            $token = new Token($this->db, $this->tableToken);
            $token->delete($hash);
            if ($setCookie) {
                if (!headers_sent()) {
                    setcookie($this->tokenName, '', time() - 3600 * 24 * 30, '/', "", false, true););
                }
            }
            return true;
        }

        return false;
    }
}