<?php
namespace Dspbee\Test\Bundle;

use Dspbee\Auth\Bundle\Login;
use Dspbee\Auth\Bundle\LoginInput;
use Dspbee\Auth\Bundle\Logout;
use Dspbee\Auth\Bundle\Registration;
use Dspbee\Auth\Bundle\RegistrationInput;
use Dspbee\Auth\Token\Token;
use Dspbee\Test\Test;

class LogoutTest extends Test
{
    public function testQuit()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $logout = new Logout($db, 'user', 'user_group', 'token', '__user__', 'user_access');

            $this->assertFalse($logout->quit());
            $this->assertTrue($logout->quit('1'));

            $registration = new Registration($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $inputReg = new RegistrationInput($db);
            $inputReg->setEmail('dspbee@gmail.com');
            $inputReg->setPassword('pass');
            $hash = $registration->register($inputReg);
            $registration->confirmRegistration($hash);
            $login = new Login($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $input = new LoginInput($db);
            $input->setEmail('dspbee@gmail.com');
            $input->setPassword('pass');
            $login->enter($input, false);
            $hash = $login->hash();

            $this->assertEquals(128, strlen($hash));

            $token = new Token($db);
            $this->assertTrue($token->verify($hash));
            $logout->quit($hash, false);
            $this->assertFalse($token->verify($hash));
        }
    }
}