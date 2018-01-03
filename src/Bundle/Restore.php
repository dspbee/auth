<?php
namespace Dspbee\Auth\Bundle;

use Dspbee\Auth\Common\Common;
use Dspbee\Auth\Common\IRestore;
use Dspbee\Auth\Token\Token;
use Dspbee\Bundle\Common\Bag\PostBag;

/**
 * Class Restore
 * @package Dspbee\Auth\Bundle
 */
class Restore extends Common implements IRestore
{
    const PENDING_TIME_IN_SEC = 43200; // 12 hours

    /**
     * @param string $email
     * @return string
     */
    public function getHash($email = '')
    {
        $this->error = '';
        if (empty($email)) {
            $post = new PostBag();
            $email = $post->fetchEscape('email', $this->db);
        } else {
            $email = $this->db->real_escape_string($email);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = $this->db->query("SELECT `id`, `status`, `hashChange` FROM `{$this->tableUser}` WHERE `email` = '{$email}' LIMIT 1");
            if ($row = $result->fetch_assoc()) {
                if ('new' == $row['status'] || 'banned' == $row['status']) {
                    $this->error = self::USER_BAD_STATUS;
                } else {
                    $now = strtotime('now');
                    $last = strtotime($row['hashChange']);

                    /**
                     * Once at PENDING_TIME
                     */
                    if (self::PENDING_TIME_IN_SEC < $now - $last) {
                        $hash = hash('sha512', mt_rand() . $email . date(DATE_ATOM));
                        $this->db->query("UPDATE `{$this->tableUser}` SET `hash` = UNHEX('{$hash}'), `hashChange` = FROM_UNIXTIME('{$now}') WHERE `id` = {$row['id']} LIMIT 1");
                        return $hash . $row['id'];
                    } else {
                        $this->error = self::PENDING_TIME;
                    }
                }
            } else {
                $this->error = self::EMAIL_NOT_FOUND;
            }
        } else {
            $this->error = self::WRONG_EMAIL;
        }

        return '';
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function validateHash($hash)
    {
        $id = intval(substr($hash, 128));
        $hash = substr($hash, 0, 128);
        if (0 < $id && '00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000' != $hash) {
            $result = $this->db->query("SELECT `hash` FROM `{$this->tableUser}` WHERE `id` = {$id} LIMIT 1");
            if ($row = $result->fetch_assoc()) {
                if (bin2hex($row['hash']) == $hash) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $hash
     * @param string $password
     * @param bool $setCookie
     * @return bool
     * @throws \ErrorException
     * @throws \HttpHeaderException
     */
    public function changePassword($hash, $password, $setCookie = true)
    {
        $id = intval(substr($hash, 128));
        $hash = substr($hash, 0, 128);

        if (0 < $id && '00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000' != $hash) {
            $result = $this->db->query("SELECT `groupId`, `status`, `hash` FROM `{$this->tableUser}` WHERE `id` = {$id} LIMIT 1");
            if ($row = $result->fetch_assoc()) {
                if ('new' == $row['status'] || 'banned' == $row['status']) {
                    $this->error = self::USER_BAD_STATUS;
                } else {
                    if (bin2hex($row['hash']) == $hash) {
                        if (!empty($password)) {
                            $password = password_hash($password, PASSWORD_DEFAULT);
                            $this->db->query("UPDATE `{$this->tableUser}` SET `password` = '{$password}', `hash` = '' WHERE `id` = {$id} LIMIT 1");

                            $token = new Token($this->db, $this->tableToken);
                            $hash = $token->create($id, $row['groupId']);
                            if (!empty($hash)) {
                                if ($setCookie) {
                                    if (headers_sent()) {
                                        throw new \HttpHeaderException("Can't set cookie, headers already sent.");
                                    } else {
                                        setcookie($this->tokenName, $hash, time() + 3600 * 24 * 30, '/', "", false, true);
                                    }
                                }
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    const WRONG_EMAIL = 'WRONG_EMAIL';
    const EMAIL_NOT_FOUND = 'EMAIL_NOT_FOUND';
    const PENDING_TIME = 'PENDING_TIME';
    const USER_BAD_STATUS = 'USER_BAD_STATUS';
}