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

    protected $id;
    protected $groupId;
    protected $data;
}