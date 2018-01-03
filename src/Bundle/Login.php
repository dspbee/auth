<?php
namespace Dspbee\Auth\Bundle;

use Dspbee\Auth\Common\Common;
use Dspbee\Auth\Common\ILogin;
use Dspbee\Auth\Token\Token;

/**
 * Class Login
 * @package Dspbee\Auth\Bundle
 */
class Login extends Common implements ILogin
{
    public function __construct(\mysqli $db, $tableUser, $tableGroup, $tableToken, $tokenName, $tableUserAccess)
    {
        parent::__construct($db, $tableUser, $tableGroup, $tableToken, $tokenName, $tableUserAccess);
        
        $this->userId = 0;
        $this->hash = '';
    }

    /**
     * @param LoginInput $input
     * @param bool $setCookie
     * @return bool
     * @throws \ErrorException
     */
    public function enter(LoginInput $input, $setCookie = true)
    {
        $this->userId = 0;
        $this->hash = '';
        $this->error = self::WRONG_EMAIL_OR_PASSWORD;
        
        if (!empty($input->email()) && !empty($input->password())) {
            if (filter_var($input->email(), FILTER_VALIDATE_EMAIL)) {
                $result = $this->db->query("SELECT `id`, `groupId`, `password`, `status` FROM `{$this->tableUser}` WHERE `email` = '{$input->email()}' LIMIT 1");
                if ($row = $result->fetch_assoc()) {
                    if (password_verify($input->password(), $row['password'])) {
                        if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
                            $hash = password_hash($input->password(), PASSWORD_DEFAULT);
                            $this->db->query("UPDATE `{$this->tableUser}` SET `password` = '{$hash}' WHERE `id` = {$row['id']} LIMIT 1");
                            if (!empty($this->db->error)) {
                                throw new \ErrorException($this->db->error);
                            }
                        }

                        switch ($row['status']) {
                            case 'new':
                                $this->error = self::NOT_ACTIVE;
                                break;
                            case 'banned':
                                $this->error = self::BANNED;
                                break;
                            default:
                                $token = new Token($this->db, $this->tableToken);
                                $hash = $token->create($row['id'], $row['groupId']);
                                if (!empty($hash)) {
                                    $this->userId = $row['id'];
                                    $this->hash = $hash;
                                    if ($setCookie) {
                                        if (headers_sent()) {
                                            throw new \ErrorException("Can't set cookie, headers already sent.");
                                        } else {
                                            setcookie($this->tokenName, $hash, time() + 3600 * 24 * 30, '/', "", false, true););
                                        }
                                    }
                                    $this->error = '';
                                    return true;
                                }
                        }
                    }
                }
            } else {
                $this->error = self::WRONG_EMAIL;
            }
        } else {
            $this->error = self::EMPTY_EMAIL_OR_PASSWORD;
        }

        return false;
    }

    /**
     * @return int
     */
    public function userId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function hash()
    {
        return $this->hash;
    }
    
    private $userId;
    private $hash;

    const EMPTY_EMAIL_OR_PASSWORD = 'EMPTY_EMAIL_OR_PASSWORD';
    const WRONG_EMAIL_OR_PASSWORD = 'WRONG_EMAIL_OR_PASSWORD';
    const WRONG_EMAIL = 'WRONG_EMAIL';
    const NOT_ACTIVE = 'NOT_ACTIVE';
    const BANNED = 'BANNED';
}