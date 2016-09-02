<?php
namespace Dspbee\Auth\Bundle;

use Dspbee\Auth\Common\Common;
use Dspbee\Auth\Common\IAccess;
use Dspbee\Auth\Token\Token;
use Dspbee\Auth\User;
use Dspbee\Bundle\Common\Bag\CookieBag;

/**
 * Class Access
 * @package Dspbee\Auth\Bundle
 */
class Access extends Common implements IAccess
{
    /**
     * @param string $hash
     * @param string $route
     * @param string $method
     * @param bool $default
     * @return User
     * @throws \ErrorException
     */
    public function getUser($hash = '', $route = '', $method = '*', $default = false): User
    {
        $user = new User();
        
        if (empty($hash)) {
            $hash = (new CookieBag())->fetch($this->tokenName);
        }
        if (!empty($hash)) {
            $token = new Token($this->db, $this->tableToken);
            if ($token->verify($hash)) {
                $access = true;
                if (!empty($route)) {
                    /**
                     * Default route access.
                     */
                    $access = $default;
                    $route = $this->db->real_escape_string($route);
                    $result = $this->db->query("SELECT `method`, `access` FROM `{$this->tableUserAccess}` WHERE `userId` = 0 AND `groupId` = 0 AND `route` = '{$route}'");
                    while ($row =  $result->fetch_assoc()) {
                        $t_access = true;
                        if ('false' == $row['access']) {
                            $t_access = false;
                        }

                        if ($method == $row['method']) {
                            $access = $t_access;
                            break;
                        } else if ('*' == $row['method']) {
                            $access = $t_access;
                        }
                    }

                    /**
                     * User access.
                     */
                    $id = intval($token->userId());
                    $result = $this->db->query("SELECT `access` FROM `{$this->tableUserAccess}` WHERE `userId` = {$id} AND `route` = '{$route}' LIMIT 1");
                    if ($row = $result->fetch_assoc()) {
                        $access = true;
                        if ('false' == $row['access']) {
                            $access = false;
                        }
                    } else if (0 < $token->groupId()) {
                        /**
                         * Group access.
                         */
                        $id = intval($token->groupId());
                        $result = $this->db->query("SELECT `access` FROM `{$this->tableUserAccess}` WHERE `groupId` = {$id} AND `route` = '{$route}' LIMIT 1");
                        if ($row = $result->fetch_assoc()) {
                            $access = true;
                            if ('false' == $row['access']) {
                                $access = false;
                            }
                        }
                    }
                }

                if ($access) {
                    $user->initFromArray(
                        [
                            'id' => $token->userId(),
                            'groupId' => $token->groupId(),
                            'data' => $token->data()
                        ]
                    );
                    $user->setStatus(User::AUTHORIZED);
                } else {
                    $user->setStatus(User::ERROR_ACCESS);
                }
            } else {
                $user->setStatus(User::ERROR_LOGIN);
            }
        }
        
        return $user;
    }
}