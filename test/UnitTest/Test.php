<?php
namespace Dspbee\Test;

class Test extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $this->assertEquals(1, 1);
    }

    protected function dataBase()
    {
        mysqli_report(MYSQLI_REPORT_STRICT);

        $null = false;

        try {
            $db = new \mysqli('127.0.0.1', 'root', '', 'test_auth');
            if (!$db->connect_error) {
                $db->query("SET NAMES 'UTF8'");
            } else {
                $null = true;
            }
        } catch (\Exception $e ) {
            $null = true;
        }

        try {
            $dbSrc = new \mysqli('127.0.0.1', 'root', '', 'src_auth');
            if (!$dbSrc->connect_error) {
                $dbSrc->query("SET NAMES 'UTF8'");
            } else {
                $null = true;
            }
        } catch (\Exception $e ) {
            $null = true;
        }

        if ($null) {
            return null;
        }

        $tableList = [];
        $result = $dbSrc->query("SHOW TABLES FROM `src_auth`");
        while ($row = $result->fetch_array()) {
            if ('phinxlog' != $row[0]) {
                $tableList[] = $row[0];
            }
        }
        $dbSrc->close();

        foreach ($tableList as $table) {
            $table = $db->real_escape_string($table);
            $db->query("CREATE TABLE `{$table}` LIKE `src_auth`.`{$table}`");
            $db->query("TRUNCATE TABLE `{$table}`");
        }

        return $db;
    }
}