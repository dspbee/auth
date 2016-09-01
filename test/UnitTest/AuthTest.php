<?php
namespace Dspbee\Test;

use Dspbee\Auth\Auth;

class AuthTest extends Test
{
    public function testRegistration()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $auth = new Auth($db);
            $this->assertInstanceOf('Dspbee\Auth\Common\IRegistration', $auth->registration());
        }
    }

    public function testLogin()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $auth = new Auth($db);
            $this->assertInstanceOf('Dspbee\Auth\Common\ILogin', $auth->login());
        }
    }

    public function testLogout()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $auth = new Auth($db);
            $this->assertInstanceOf('Dspbee\Auth\Common\ILogout', $auth->logout());
        }
    }

    public function testCheck()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $auth = new Auth($db);
            $this->assertInstanceOf('Dspbee\Auth\Common\ICheck', $auth->check());
        }
    }

    public function testRestore()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $auth = new Auth($db);
            $this->assertInstanceOf('Dspbee\Auth\Common\IRestore', $auth->restore());
        }
    }

    public function testAccess()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $auth = new Auth($db);
            $this->assertInstanceOf('Dspbee\Auth\Common\IAccess', $auth->access());
        }
    }
}