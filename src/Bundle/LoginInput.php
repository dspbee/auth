<?php
namespace Dspbee\Auth\Bundle;

use Dspbee\Bundle\Common\Bag\PostBag;

class LoginInput
{
    public function __construct(\mysqli $db)
    {
        $this->db = $db;
        $post = new PostBag();

        $this->email = $post->fetchEscape('email', $this->db);
        $this->password = $post->fetchEscape('password', $this->db);
        if (3 > strlen($this->password)) {
            $this->password = '';
        }
    }
    
    public function email()
    {
        return $this->email;
    }
    
    public function password()
    {
        return $this->password;
    }
    
    public function setEmail($email)
    {
        $this->email = $this->db->real_escape_string($email);
    }
    
    public function setPassword($password)
    {
        if (3 > strlen($password)) {
            $this->password = '';
        } else {
            $this->password = $this->db->real_escape_string($password);
        }
    }
    
    private $db;
    
    private $email;
    private $password;
}