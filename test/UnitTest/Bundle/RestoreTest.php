<?php
namespace Dspbee\Test\Bundle;

use Dspbee\Auth\Bundle\Login;
use Dspbee\Auth\Bundle\LoginInput;
use Dspbee\Auth\Bundle\Registration;
use Dspbee\Auth\Bundle\RegistrationInput;
use Dspbee\Auth\Bundle\Restore;
use Dspbee\Test\Test;

class RestoreTest extends Test
{
    public function testGetHash()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $restore = new Restore($db, 'user', 'user_group', 'token', '__user__', 'user_access');

            $this->assertEquals('', $restore->getHash());
            $this->assertEquals(Restore::WRONG_EMAIL, $restore->error());

            $this->assertEquals('', $restore->getHash('dspbee@gmail.com'));
            $this->assertEquals(Restore::EMAIL_NOT_FOUND, $restore->error());


            $registration = new Registration($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $inputReg = new RegistrationInput($db);
            $inputReg->setEmail('dspbee@gmail.com');
            $inputReg->setPassword('pass');
            $hash = $registration->register($inputReg);

            $this->assertEquals('', $restore->getHash('dspbee@gmail.com'));
            $this->assertEquals(Restore::USER_BAD_STATUS, $restore->error());

            $registration->confirmRegistration($hash);

            $hash = $restore->getHash('dspbee@gmail.com');
            $this->assertTrue((128 < strlen($hash)));
            $this->assertEquals('', $restore->error());

            $hash = $restore->getHash('dspbee@gmail.com');
            $this->assertEquals('', $hash);
            $this->assertEquals(Restore::PENDING_TIME, $restore->error());
        }
    }

    public function testValidateHash()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $restore = new Restore($db, 'user', 'user_group', 'token', '__user__', 'user_access');

            $registration = new Registration($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $inputReg = new RegistrationInput($db);
            $inputReg->setEmail('dspbee@gmail.com');
            $inputReg->setPassword('pass');
            $registration->confirmRegistration($registration->register($inputReg));

            $this->assertNotTrue($restore->validateHash('1'));
            $this->assertNotTrue($restore->validateHash(''));
            $this->assertNotTrue($restore->validateHash('00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'));
            $this->assertNotTrue($restore->validateHash('000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001'));
            $this->assertNotTrue($restore->validateHash('000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000011'));

            $hash = $restore->getHash('dspbee@gmail.com');

            $this->assertNotTrue($restore->validateHash('1'));
            $this->assertNotTrue($restore->validateHash(''));
            $this->assertNotTrue($restore->validateHash('00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'));
            $this->assertNotTrue($restore->validateHash('000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001'));
            $this->assertNotTrue($restore->validateHash('000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000011'));
            $this->assertNotTrue($restore->validateHash($hash . '1'));

            $this->assertTrue($restore->validateHash($hash));
        }
    }
    
    public function testChangePassword()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $restore = new Restore($db, 'user', 'user_group', 'token', '__user__', 'user_access');

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

            $this->assertTrue($login->enter($input, false));
            $hash = $login->hash();
            $this->assertEquals(128, strlen($hash));

            $hash = $restore->getHash($input->email());
            $this->assertFalse($restore->changePassword('', '123', false));
            $this->assertFalse($restore->changePassword('00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000', '123', false));
            $this->assertFalse($restore->changePassword('000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001', '123', false));
            $this->assertFalse($restore->changePassword('123', '123', false));
            $this->assertTrue($restore->changePassword($hash, '123', false));

            $this->assertFalse($login->enter($input, false));
            $hash = $login->hash();
            $this->assertEquals(0, strlen($hash));
            $this->assertEquals('', $login->error());

            $input->setPassword('123');
            $this->assertTrue($login->enter($input, false));
            $hash = $login->hash();
            $this->assertEquals(128, strlen($hash));
            $this->assertEquals('', $login->error());
        }
    }
}