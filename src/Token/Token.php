<?php
/**
 * @license MIT
 * @author Igor Sorokin <dspbee@pivasic.com>
 */
namespace Dspbee\Auth\Token;

/**
 * Manage token.
 *
 * Class Token
 * @package Dspbee\Auth\Token
 */
class Token
{
    /**
     * One month.
     */
    const TTL = 60 * 60 * 24 * 30;

    /**
     * @param \mysqli $db
     * @param string $table
     */
    public function __construct(\mysqli $db, $table = 'token')
    {
        $this->db = $db;
        $this->table = $this->db->real_escape_string($table);
        $this->init();
    }

    /**
     * Create new token.
     *
     * @param $userId
     * @param $groupId
     * @param string|null $data
     * @param int $use - 255 means unlimited usages.
     *
     * @return string|null  Token hash or null if create fail
     * @throws \ErrorException
     */
    public function create($userId, $groupId, $data = null, $use = 255)
    {
        $userId = intval($userId);
        $groupId = intval($groupId);
        $dataSafe = $this->db->real_escape_string(serialize($data));
        $use = intval($use);
        if (255 < $use) {
            $use = 255;
        }

        $id = 0;
        $token = '';
        if (0 < $use) {
            $token = hash('sha512', $userId . $groupId . $dataSafe . date(DATE_ATOM) . $this->generate());
            $result = $this->db->query("SELECT `id` FROM `{$this->table}` WHERE `token` = UNHEX('{$token}') LIMIT 1");
            if (!empty($this->db->error)) {
                throw new \ErrorException($this->db->error);
            }
            while ($row = $result->fetch_assoc()) {
                $token = hash('sha512', $userId . $groupId . $dataSafe . date(DATE_ATOM) . $this->generate());
                $result = $this->db->query("SELECT `id` FROM `{$this->table}` WHERE `token` = UNHEX('{$token}') LIMIT 1");
            }

            $date = strtotime(date('Y-m-d'));
            $this->db->query("INSERT INTO `{$this->table}` SET `token` = UNHEX('{$token}'), `use` = {$use}, `userId` = {$userId}, `groupId` = {$groupId}, `data` = '{$dataSafe}', `date` = FROM_UNIXTIME('{$date}')");
            if (!empty($this->db->error)) {
                throw new \ErrorException($this->db->error);
            }
            $id = $this->db->insert_id;
        }

        $this->init();

        if (0 < $id) {
            $this->id = $id;
            $this->token = $token;
            $this->userId = $userId;
            $this->groupId = $groupId;
            $this->data = $data;
        }

        return $this->token;
    }

    /**
     * Check token.
     *
     * @param string $hash
     *
     * @return bool
     * @throws \ErrorException
     */
    public function verify($hash)
    {
        $hash = $this->db->real_escape_string($hash);
        $this->init();
        $status = false;

        /**
         * Initialize.
         */
        $result = $this->db->query("SELECT `id`, `use`, `userId`, `groupId`, `data` FROM `{$this->table}` WHERE `token` = UNHEX('{$hash}') LIMIT 1");
        if (!empty($this->db->error)) {
            throw new \ErrorException($this->db->error);
        }
        if ($row = $result->fetch_assoc()) {
            /**
             * Check uses.
             */
            if (0 < $row['use']) {
                $status = true;

                $this->id = $row['id'];
                $this->token = $hash;
                $this->userId = $row['userId'];
                $this->groupId = $row['groupId'];
                $this->data = unserialize($row['data']);

                if (255 != $row['use']) {
                    $this->db->query("UPDATE `{$this->table}` SET `use` = `use` - 1 WHERE `id` = {$row['id']} LIMIT 1");
                }
            } else {
                $this->db->query("DELETE FROM `{$this->table}` WHERE `id` = {$row['id']} LIMIT 1");
            }
        }

        return $status;
    }

    /**
     * Delete token.
     *
     * @param null|string $hash
     * @param bool $deleteOld
     * @throws \ErrorException
     */
    public function delete($hash, $deleteOld = false)
    {
        $hash = $this->db->real_escape_string($hash);
        $this->db->query("DELETE FROM `{$this->table}` WHERE `token` = UNHEX('{$hash}') LIMIT 1");
        if (!empty($this->db->error)) {
            throw new \ErrorException($this->db->error);
        }
        $this->init();
        if ($deleteOld) {
            $date = strtotime("now") - self::TTL;
            $this->db->query("DELETE FROM `{$this->table}` WHERE `date` <= FROM_UNIXTIME('{$date}')");
            if (!empty($this->db->error)) {
                throw new \ErrorException($this->db->error);
            }
        }
    }

    /**
     * @param int $id
     * @throws \ErrorException
     */
    public function deleteByUserId($id)
    {
        $id = intval($id);
        $this->db->query("DELETE FROM `{$this->table}` WHERE `userId` = {$id}");
        if (!empty($this->db->error)) {
            throw new \ErrorException($this->db->error);
        }
        $this->init();
    }

    /**
     * Get user ID.
     *
     * @return int
     */
    public function userId()
    {
        return $this->userId;
    }

    /**
     * Get group ID.
     *
     * @return int
     */
    public function groupId()
    {
        return $this->groupId;
    }

    /**
     * Get custom data.
     *
     * @return string
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Generate random sequence.
     *
     * @param int $length
     *
     * @return string
     */
    private function generate($length = 32)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    /**
     * Clear values.
     */
    private function init()
    {
        $this->id = 0;
        $this->token = '';
        $this->userId = 0;
        $this->groupId = 0;
        $this->data = null;
    }

    private $db;
    private $table;

    private $id;
    private $token;
    private $userId;
    private $groupId;
    private $data;
}