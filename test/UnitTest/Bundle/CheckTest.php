<?php
namespace Dspbee\Test\Bundle;

use Dspbee\Auth\Bundle\Check;
use Dspbee\Auth\Bundle\Registration;
use Dspbee\Auth\Bundle\RegistrationInput;
use Dspbee\Test\Test;

class CheckTest extends Test
{
    public function testIsRegistered()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $check = new Check($db, 'user', 'user_group', 'token', '__user__', 'user_access');
            $this->assertFalse($check->isRegistered());
            
                
            $registration = new Registration($db, 'user', 'user_group', 'token', '__user__', 'user_access');

            $input = new RegistrationInput($db);

            $input->setEmail('dspbee@gmail.com');
            $input->setPassword('pass');
            
            $check = new Check($db, 'user', 'user_group', 'token', '__user__', 'user_access', $input->email());
            $this->assertFalse($check->isRegistered());
            
            $registration->register($input);

            $this->assertTrue($check->isRegistered());
        }
    }
}