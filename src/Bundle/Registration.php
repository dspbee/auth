<?php
namespace Dspbee\Auth\Bundle;

use Dspbee\Auth\Common\Common;
use Dspbee\Auth\Common\IRegistration;

/**
 * Class Registration
 * @package Dspbee\System\Auth\Mysql
 */
class Registration extends Common implements IRegistration
{
    /**
     * Validate data and add new user.
     *
     * @param RegistrationInput $input
     * @return string
     * @throws \ErrorException
     */
    public function register(RegistrationInput $input)
    {
        $this->error = '';
        $this->hash = '';

        $this->groupId = $input->groupId();
        $this->email = $input->email();
        $this->password = $input->password();

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            if (empty($this->password)) {
                $this->error = self::EMPTY_PASSWORD;
            } else {
                if (!(new Check($this->db, $this->tableUser, $this->tableGroup, $this->tableToken, $this->tokenName, $this->tableUserAccess, $this->email))->isRegistered()) {
                    $this->hash = hash('sha512', mt_rand() . $this->email . date(DATE_ATOM));
                    $id = $this->addUser();
                    if (0 < $id) {
                        $this->hash .= $id;
                    } else {
                        $this->hash = '';
                        $this->error = self::FAIL_ADD_USER;
                    }
                } else {
                    $this->error = self::EMAIL_EXIST;
                }
            }
        } else {
            $this->error = self::WRONG_EMAIL;
        }
        
        return $this->hash;
    }

    /**
     * Activate user.
     * 
     * @param string $hash
     * @return bool
     * @throws \ErrorException
     */
    public function confirmRegistration($hash)
    {
        if (!empty($hash)) {
            $userId = intval(substr($hash, 128));
            $hash = substr($hash, 0, 128);
            if (0 < $userId && '00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000' != $hash) {
                $result = $this->db->query("SELECT `hash` FROM `{$this->tableUser}` WHERE `id` = {$userId} LIMIT 1");
                if (!empty($this->db->error)) {
                    throw new \ErrorException($this->db->error);
                }
                if ($row = $result->fetch_assoc()) {
                    if (bin2hex($row['hash']) == $hash) {
                        $this->db->query("UPDATE `{$this->tableUser}` SET `status` = 'active', `hash` = '' WHERE `id` = {$userId} LIMIT 1");
                        if (!empty($this->db->error)) {
                            throw new \ErrorException($this->db->error);
                        }
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Add default user data.
     * 
     * @return int
     * @throws \ErrorException
     */
    protected function addUser()
    {
        $this->db->query("
                INSERT INTO `{$this->tableUser}` SET
                  `groupId` = {$this->groupId},
                  `email` = '{$this->email}',
                  `password` = '{$this->password}',
                  `status` = 'new',
                  `hash` = UNHEX('{$this->hash}')
                ");

        if (!empty($this->db->error)) {
            throw new \ErrorException($this->db->error);
        }

        return $this->db->insert_id;
    }

    protected $groupId;
    protected $email;
    protected $password;
    protected $hash;

    const WRONG_EMAIL = 'WRONG_EMAIL';
    const EMPTY_PASSWORD = 'EMPTY_PASSWORD';
    const EMAIL_EXIST = 'EMAIL_EXIST';
    const FAIL_ADD_USER = 'FAIL_ADD_USER';
}