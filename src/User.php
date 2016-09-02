<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth;

use Dspbee\Bundle\Data\TDataInit;

/**
 * Class User
 * @package Dspbee\Auth
 */
class User
{
    use TDataInit;

    public function __construct()
    {
        $this->id = 0;
        $this->groupId = 0;
        $this->data = null;
        
        $this->status = '';
    }

    /**
     * User id.
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * User group id.
     * @return int
     */
    public function groupId()
    {
        return $this->groupId;
    }

    /**
     * Stored user data in token.
     * @return null
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Get user status.
     * @return string
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Set user status.
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    protected $id;
    protected $groupId;
    protected $data;
    
    private $status;
    
    const AUTHORIZED = 'AUTHORIZED';
    const ERROR_LOGIN = 'ERROR_LOGIN';
    const ERROR_ACCESS = 'ERROR_ACCESS';
}