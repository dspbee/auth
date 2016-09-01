<?php
namespace Dspbee\Test\Token;

use Dspbee\Test\Test;
use Dspbee\Auth\Token\Token;

class TokenTest extends Test
{
    public function testCreate()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $token = new Token($db);

            $this->assertEquals('', $token->create(1, 1, '', 0));

            $this->assertEquals(128, strlen($token->create(1, 2, ['test' => 'array'])));
            $this->assertEquals(1, $token->userId());
            $this->assertEquals(2, $token->groupId());
            $this->assertEquals(['test' => 'array'], $token->data());
        }
    }

    public function testVerify()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $tokenCreate = new Token($db);
            $hash = $tokenCreate->create(1, 2, ['test' => 'array']);


            $token = new Token($db);

            $this->assertEquals(0, $token->userId());
            $this->assertEquals(0, $token->groupId());
            $this->assertNull($token->data());

            $this->assertNotTrue($token->verify('00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'));

            $this->assertTrue($token->verify($hash));

            $this->assertEquals(1, $token->userId());
            $this->assertEquals(2, $token->groupId());
            $this->assertEquals(['test' => 'array'], $token->data());
        }
    }

    public function testVerifyWithUse()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $tokenCreate = new Token($db);
            $hash = $tokenCreate->create(1, 2, ['test' => 'array'], 2);


            $token = new Token($db);

            $this->assertEquals(0, $token->userId());
            $this->assertEquals(0, $token->groupId());
            $this->assertNull($token->data());


            $this->assertTrue($token->verify($hash));

            $this->assertEquals(1, $token->userId());
            $this->assertEquals(2, $token->groupId());
            $this->assertEquals(['test' => 'array'], $token->data());


            $this->assertTrue($token->verify($hash));

            $this->assertEquals(1, $token->userId());
            $this->assertEquals(2, $token->groupId());
            $this->assertEquals(['test' => 'array'], $token->data());


            $this->assertNotTrue($token->verify($hash));

            $this->assertEquals(0, $token->userId());
            $this->assertEquals(0, $token->groupId());
            $this->assertNull($token->data());

            $result = $db->query("SELECT COUNT(*) as `count` FROM `token`");
            if ($row = $result->fetch_assoc()) {
                $this->assertEquals(0, $row['count']);
            }
        }
    }

    public function testDelete()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $tokenCreate = new Token($db);
            $hash = $tokenCreate->create(1, 2, ['test' => 'array'], 2);


            $token = new Token($db);

            $this->assertEquals(0, $token->userId());
            $this->assertEquals(0, $token->groupId());
            $this->assertNull($token->data());


            $this->assertTrue($token->verify($hash));

            $date = strtotime('now') - Token::TTL;
            $db->query("INSERT INTO `token` SET `date` = FROM_UNIXTIME('{$date}')");

            $token->delete($hash);

            $this->assertEquals(0, $token->userId());
            $this->assertEquals(0, $token->groupId());
            $this->assertNull($token->data());

            $result = $db->query("SELECT COUNT(*) as `count` FROM `token`");
            if ($row = $result->fetch_assoc()) {
                $this->assertEquals(1, $row['count']);
            }

            $token->delete($hash, true);


            $result = $db->query("SELECT COUNT(*) as `count` FROM `token`");
            if ($row = $result->fetch_assoc()) {
                $this->assertEquals(0, $row['count']);
            }
        }
    }

    public function testDeleteByUserId()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $tokenCreate = new Token($db);
            $hash = $tokenCreate->create(1, 2, ['test' => 'array'], 2);


            $token = new Token($db);

            $this->assertEquals(0, $token->userId());
            $this->assertEquals(0, $token->groupId());
            $this->assertNull($token->data());


            $this->assertTrue($token->verify($hash));
            $userId = $token->userId();

            $token->deleteByUserId($userId);

            $this->assertEquals(0, $token->userId());
            $this->assertEquals(0, $token->groupId());
            $this->assertNull($token->data());

            $result = $db->query("SELECT COUNT(*) as `count` FROM `token`");
            if ($row = $result->fetch_assoc()) {
                $this->assertEquals(0, $row['count']);
            }
        }
    }

    public function testUserId()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $token = new Token($db);
            $this->assertEquals(0, $token->userId());
        }
    }

    public function testGroupId()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $token = new Token($db);
            $this->assertEquals(0, $token->groupId());
        }
    }

    public function testData()
    {
        $db = $this->dataBase();
        if (null !== $db) {
            $token = new Token($db);
            $this->assertNull($token->data());
        }
    }
}