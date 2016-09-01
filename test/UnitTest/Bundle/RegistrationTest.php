<?php
namespace Dspbee\Test\Bundle;

use Dspbee\Auth\Bundle\Registration;
use Dspbee\Auth\Bundle\RegistrationInput;
use Dspbee\Test\Test;

class RegistrationTest extends Test
{
    public function testRegister()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $registration = new Registration($db, 'user', 'user_group', 'token', '__user__', 'user_access');

            $input = new RegistrationInput($db);
            
            $hash = $registration->register($input);
            $this->assertEmpty($hash);
            $this->assertEquals(Registration::WRONG_EMAIL, $registration->error());

            $input->setEmail('dspbee@gmail.com');
            $input->setPassword('pa');
            $hash = $registration->register($input);
            $this->assertEmpty($hash);
            $this->assertEquals(Registration::EMPTY_PASSWORD, $registration->error());

            $input->setEmail('dspbee@gmail.com');
            $input->setPassword('pass');
            $hash = $registration->register($input);
            $this->assertTrue((128 < strlen($hash)));
            $this->assertEmpty($registration->error());

            $hash = $registration->register($input);
            $this->assertEmpty($hash);
            $this->assertEquals(Registration::EMAIL_EXIST, $registration->error());
        }
    }

    public function testConfirmRegistration()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $registration = new Registration($db, 'user', 'user_group', 'token', '__user__', 'user_access');

            $input = new RegistrationInput($db);

            $input->setEmail('dspbee@gmail.com');
            $input->setPassword('pass');
            $hash = $registration->register($input);


            $this->assertNotTrue($registration->confirmRegistration('1'));
            $this->assertNotTrue($registration->confirmRegistration(''));
            $this->assertNotTrue($registration->confirmRegistration('00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'));
            $this->assertNotTrue($registration->confirmRegistration('000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001'));
            $this->assertNotTrue($registration->confirmRegistration('000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000011'));
            $this->assertNotTrue($registration->confirmRegistration($hash . '1'));

            $this->assertTrue($registration->confirmRegistration($hash));
            $this->assertNotTrue($registration->confirmRegistration($hash));

            $userId = intval(substr($hash, 128));
            $result = $db->query("SELECT `status` FROM `user` WHERE `id` = {$userId} LIMIT 1");
            if ($row = $result->fetch_assoc()) {
                $this->assertEquals('active', $row['status']);
            }
        }
    }
}