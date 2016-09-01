<?php
namespace Dspbee\Test;

use Dspbee\Auth\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $user = new User();
        $this->assertEquals(0, $user->id());

        $user->initFromArray(
            [
                'id' => 1
            ]
        );
        $this->assertEquals(1, $user->id());
    }

    public function testGroupId()
    {
        $user = new User();
        $this->assertEquals(0, $user->groupId());

        $user->initFromArray(
            [
                'groupId' => 1
            ]
        );
        $this->assertEquals(1, $user->groupId());
    }

    public function testData()
    {
        $user = new User();
        $this->assertEquals(null, $user->data());

        $user->initFromArray(
            [
                'data' => 'test'
            ]
        );
        $this->assertEquals('test', $user->data());
    }
}