<?php
namespace Dspbee\Test\Bundle;

use Dspbee\Auth\Bundle\Login;
use Dspbee\Auth\Bundle\LoginInput;
use Dspbee\Auth\Bundle\Registration;
use Dspbee\Auth\Bundle\RegistrationInput;
use Dspbee\Test\Test;

class LoginTest extends Test
{
    public function testEnter()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $login = new Login($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            
            $input = new LoginInput($db);
            $this->assertFalse($login->enter($input, false));
            $this->assertEquals(Login::EMPTY_EMAIL_OR_PASSWORD, $login->error());

            $input->setEmail('dspbee@gmail.com');
            $input->setPassword('pa');
            $this->assertFalse($login->enter($input, false));
            $this->assertEquals(Login::EMPTY_EMAIL_OR_PASSWORD, $login->error());
            $this->assertEquals(0, $login->userId());
            $this->assertEquals('', $login->hash());

            $input->setEmail('dspbee@gmail_com');
            $input->setPassword('pass');
            $this->assertFalse($login->enter($input, false));
            $this->assertEquals(Login::WRONG_EMAIL, $login->error());
            $this->assertEquals(0, $login->userId());
            $this->assertEquals('', $login->hash());

            $input->setEmail('dspbee@gmail.com');
            $input->setPassword('pass');
            $this->assertFalse($login->enter($input, false));
            $this->assertEquals(Login::WRONG_EMAIL_OR_PASSWORD, $login->error());
            $this->assertEquals(0, $login->userId());
            $this->assertEquals('', $login->hash());


            $registration = new Registration($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $inputReg = new RegistrationInput($db);
            $inputReg->setEmail('dspbee@gmail.com');
            $inputReg->setPassword('pass');
            $hash = $registration->register($inputReg);
            $this->assertFalse($login->enter($input, false));
            $this->assertEquals(Login::NOT_ACTIVE, $login->error());
            $this->assertEquals(0, $login->userId());
            $this->assertEquals('', $login->hash());

            $registration->confirmRegistration($hash);
            $this->assertTrue($login->enter($input, false));
            $this->assertEquals('', $login->error());
            $this->assertEquals(1, $login->userId());
            $this->assertEquals(128, strlen($login->hash()));

            $db->query("UPDATE `user` SET `status` = 'banned' WHERE `id` = 1 LIMIT 1");
            $this->assertFalse($login->enter($input, false));
            $this->assertEquals(Login::BANNED, $login->error());
            $this->assertEquals(0, $login->userId());
            $this->assertEquals('', $login->hash());
        }
    }

    public function testUserId()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $login = new Login($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $this->assertEquals(0, $login->userId());
        }
    }
}