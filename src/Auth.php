<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth;

use Dspbee\Auth\Bundle\Access;
use Dspbee\Auth\Bundle\Check;
use Dspbee\Auth\Bundle\Login;
use Dspbee\Auth\Bundle\Logout;
use Dspbee\Auth\Bundle\Registration;
use Dspbee\Auth\Bundle\Restore;
use Dspbee\Auth\Common\IAccess;
use Dspbee\Auth\Common\ICheck;
use Dspbee\Auth\Common\ILogin;
use Dspbee\Auth\Common\ILogout;
use Dspbee\Auth\Common\IRegistration;
use Dspbee\Auth\Common\IRestore;

/**
 * Class Auth
 * @package Dspbee\Auth
 */
class Auth
{
    public function __construct(\mysqli $db)
    {
        $this->db = $db;
        
        $this->tableUser = 'user';
        $this->tableGroup = 'user_group';
        $this->tableToken = 'user_token';
        $this->tokenName = '__user__';
        $this->tableUserAccess = 'user_access';
    }

    /**
     * @return IRegistration
     */
    public function registration(): IRegistration
    {
        return new Registration($this->db, $this->tableUser, $this->tableGroup, $this->tableToken, $this->tokenName, $this->tableUserAccess);
    }

    /**
     * @return ILogin
     */
    public function login(): ILogin
    {
        return new Login($this->db, $this->tableUser, $this->tableGroup, $this->tableToken, $this->tokenName, $this->tableUserAccess);
    }

    /**
     * @return ILogout
     */
    public function logout(): ILogout
    {
        return new Logout($this->db, $this->tableUser, $this->tableGroup, $this->tableToken, $this->tokenName, $this->tableUserAccess);
    }

    /**
     * @param string $email - if parameter empty then value searched in GET and POST arrays.
     * @return ICheck
     */
    public function check($email = ''): ICheck
    {
        return new Check($this->db, $this->tableUser, $this->tableGroup, $this->tableToken, $this->tokenName, $this->tableUserAccess, $email);
    }

    /**
     * @return IRestore
     */
    public function restore(): IRestore
    {
        return new Restore($this->db, $this->tableUser, $this->tableGroup, $this->tableToken, $this->tokenName, $this->tableUserAccess);
    }

    /**
     * @return IAccess
     */
    public function access(): IAccess
    {
        return new Access($this->db, $this->tableUser, $this->tableGroup, $this->tableToken, $this->tokenName, $this->tableUserAccess);
    }

    protected $db;

    protected $tableUser;
    protected $tableGroup;
    protected $tableToken;
    protected $tokenName;
    protected $tableUserAccess;
}