<?php
namespace Dspbee\Test\Bundle;

use Dspbee\Auth\Bundle\Access;
use Dspbee\Auth\Bundle\Login;
use Dspbee\Auth\Bundle\LoginInput;
use Dspbee\Auth\Bundle\Registration;
use Dspbee\Auth\Bundle\RegistrationInput;
use Dspbee\Test\Test;

class AccessTest extends Test
{
    public function testGetUser()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $access = new Access($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $user = $access->getUser();
            
            $this->assertEquals(0, $user->id());
            $this->assertEquals(0, $user->groupId());
            $this->assertEquals(null, $user->data());


            $registration = new Registration($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $inputReg = new RegistrationInput($db);
            $inputReg->setEmail('dspbee@gmail.com');
            $inputReg->setPassword('pass');
            $inputReg->setGroupId(2);
            $hash = $registration->register($inputReg);
            $registration->confirmRegistration($hash);
            $login = new Login($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $input = new LoginInput($db);
            $input->setEmail('dspbee@gmail.com');
            $input->setPassword('pass');
            $login->enter($input, false);
            $hash = $login->hash();


            $user = $access->getUser($hash);
            $this->assertEquals(1, $user->id());
            $this->assertEquals(2, $user->groupId());
            $this->assertEquals(null, $user->data());

            $db->query("INSERT INTO `user_access` SET `userId` = 0, `groupId` = 0, `route` = 'index', `method` = '*', `access` = 'true'");
            $db->query("INSERT INTO `user_access` SET `userId` = 0, `groupId` = 0, `route` = 'index', `method` = 'GET', `access` = 'true'");
            $db->query("INSERT INTO `user_access` SET `userId` = 1, `groupId` = 0, `route` = 'index', `method` = '*', `access` = 'true'");
            $db->query("INSERT INTO `user_access` SET `userId` = 1, `groupId` = 0, `route` = 'index', `method` = '*', `access` = 'true'");
            $db->query("INSERT INTO `user_access` SET `userId` = 0, `groupId` = 2, `route` = 'index', `method` = '*', `access` = 'true'");

            $user = $access->getUser($hash, 'index');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', '*');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'GET');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'POST');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'PUT');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'AJAX');
            $this->assertEquals(1, $user->id());


            $db->query("TRUNCATE TABLE `user_access`");
            $db->query("INSERT INTO `user_access` SET `userId` = 0, `groupId` = 0, `route` = 'index', `method` = 'GET', `access` = 'true'");
            $db->query("INSERT INTO `user_access` SET `userId` = 2, `groupId` = 0, `route` = 'index', `method` = '*', `access` = 'true'");
            $db->query("INSERT INTO `user_access` SET `userId` = 0, `groupId` = 2, `route` = 'index', `method` = '*', `access` = 'true'");

            $user = $access->getUser($hash, 'index');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', '*');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'GET');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'POST');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'PUT');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'AJAX');
            $this->assertEquals(1, $user->id());


            $db->query("TRUNCATE TABLE `user_access`");
            $db->query("INSERT INTO `user_access` SET `userId` = 0, `groupId` = 0, `route` = 'index', `method` = 'GET', `access` = 'true'");
            $db->query("INSERT INTO `user_access` SET `userId` = 2, `groupId` = 0, `route` = 'index', `method` = '*', `access` = 'true'");

            $user = $access->getUser($hash, 'index');
            $this->assertEquals(0, $user->id());

            $user = $access->getUser($hash, 'index', '*');
            $this->assertEquals(0, $user->id());

            $user = $access->getUser($hash, 'index', 'GET');
            $this->assertEquals(1, $user->id());

            $user = $access->getUser($hash, 'index', 'POST');
            $this->assertEquals(0, $user->id());

            $user = $access->getUser($hash, 'index', 'PUT');
            $this->assertEquals(0, $user->id());

            $user = $access->getUser($hash, 'index', 'AJAX');
            $this->assertEquals(0, $user->id());
        }
    }
}