<?php
namespace Dspbee\Auth\Bundle;

use Dspbee\Bundle\Common\Bag\PostBag;

class RegistrationInput
{
    public function __construct(\mysqli $db)
    {
        $this->db = $db;
        
        $post = new PostBag();
        $this->groupId = $post->fetchInt('groupId');
        $this->email = $post->fetchEscape('email', $this->db);
        $this->password = '';
        $password = $post->fetch('password');
        if (3 <= strlen($password)) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);   
        }
    }
    
    public function groupId()
    {
        return $this->groupId;
    }
    
    public function email()
    {
        return $this->email;
    }
    
    public function password()
    {
        return $this->password;
    }
    
    public function setGroupId($groupId)
    {
        $this->groupId = intval($groupId);
    }
    
    public function setEmail($email)
    {
        $this->email = $this->db->real_escape_string($email);
    }
    
    public function setPassword($password)
    {
        $this->password = '';
        if (3 <= strlen($password)) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }
    }

    private $db;
    
    private $groupId;
    private $email;
    private $password;
}