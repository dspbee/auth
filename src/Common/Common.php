<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth\Common;

/**
 * Class Common
 * @package Dspbee\Auth\Common
 */
class Common
{
    /**
     * Common constructor.
     * @param \mysqli $db
     * @param string $tableUser
     * @param string $tableGroup
     * @param string $tableToken
     * @param string $tokenName
     * @param string $tableUserAccess
     */
    public function __construct(\mysqli $db, $tableUser, $tableGroup, $tableToken, $tokenName, $tableUserAccess)
    {
        $this->db = $db;
        $this->tableUser = $this->db->real_escape_string($tableUser);
        $this->tableGroup = $this->db->real_escape_string($tableGroup);
        $this->tableToken = $this->db->real_escape_string($tableToken);
        $this->tokenName = $this->db->real_escape_string($tokenName);
        $this->tableUserAccess = $this->db->real_escape_string($tableUserAccess);
        $this->error = '';
    }

    /**
     * @return string
     */
    public function error()
    {
        return $this->error;
    }

    protected $db;
    protected $tableUser;
    protected $tableGroup;
    protected $tableToken;
    protected $tokenName;
    protected $tableUserAccess;
    protected $error;
}