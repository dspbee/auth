<?php
namespace Dspbee\Auth\Bundle;

use Dspbee\Auth\Common\Common;
use Dspbee\Auth\Common\ICheck;
use Dspbee\Bundle\Common\Bag\GetBag;
use Dspbee\Bundle\Common\Bag\PostBag;

/**
 * Class Registration
 * @package Dspbee\System\Auth\Mysql
 */
class Check extends Common implements ICheck
{
    public function __construct(\mysqli $db, $tableUser, $tableGroup, $tableToken, $tokenName, $tableUserAccess, $email = '')
    {
        parent::__construct($db, $tableUser, $tableGroup, $tableToken, $tokenName, $tableUserAccess);

        if (empty($email)) {
            $post = new PostBag();
            if ($post->has('email')) {
                $email = $post->fetchEscape('email', $this->db);
            } else {
                $get = new GetBag();
                if ($get->has('email')) {
                    $email = $get->fetchEscape('email', $this->db);
                }
            }
        } else {
            $email = $this->db->real_escape_string($email);
        }
        $this->email = $email;
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function isRegistered()
    {
        if (empty($this->email)) {
            return false;    
        }
        
        $result = $this->db->query("SELECT 1 FROM `{$this->tableUser}` WHERE `email` = '{$this->email}' LIMIT 1");
        if (!empty($this->db->error)) {
            throw new \ErrorException($this->db->error);
        }
        return 1 == $result->num_rows;
    }

    protected $email;
}