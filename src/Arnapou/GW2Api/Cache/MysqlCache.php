<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * SQL schema
 *

  CREATE TABLE IF NOT EXISTS `cache` (
  `hash` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` longblob NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`hash`),
  KEY `expiration` (`expiration`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 */

namespace Arnapou\GW2Api\Cache;

use Arnapou\GW2Api\Exception\Exception;

class MysqlCache implements CacheInterface
{
    /**
     *
     * @var int
     */
    protected $gcProbability = 100;

    /**
     *
     * @var int
     */
    protected $gcDivisor = 100000;

    /**
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     *
     * @var string
     */
    protected $table;

    /**
     *
     * @var array
     */
    protected $prepared;

    /**
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo, $table)
    {
        $this->pdo   = $pdo;
        $this->table = $table;
    }

    /**
     * Ran when php exits : automatically run of GC if conditions are met
     */
    public function __destruct()
    {
        $rand = mt_rand(1, $this->gcDivisor);
        if ($rand <= $this->gcProbability) {
            $this->runGarbageCollector();
        }
    }

    /**
     * Run the garbage collector which clean expired files
     */
    public function runGarbageCollector()
    {
        $this->pdo->exec('DELETE FROM `' . $this->table . '̀  WHERE expiration < ' . time());
    }

    /**
     * Set the probability for the garbage collector to clean expired
     * data (gcProbability/gcDivisor) when the script finishes.
     *
     * If gcProbability = 0 then the garbage collector will never run.
     *
     * If gcProbability > gcDivisor then the garbage collector will always run.
     *
     * @param int $gcProbability
     * @param int $gcDivisor
     */
    public function setGarbageCollectorParameters($gcProbability, $gcDivisor)
    {
        if ($gcDivisor < 1) {
            throw new Exception('gcDivisor should be strictly > 0.');
        }
        if ($gcDivisor < 0) {
            throw new Exception('gcProbability cannot be negative.');
        }
        $this->gcDivisor     = $gcDivisor;
        $this->gcProbability = $gcProbability;
    }

    protected function hash($key)
    {
        return hash('sha256', $key);
    }

    public function get($key)
    {
        $prepared = $this->getPreparedGet();
        $prepared->bindValue('hash', $this->hash($key), \PDO::PARAM_STR);
        $prepared->bindValue('expiration', time(), \PDO::PARAM_INT);
        $prepared->execute();

        $row = $prepared->fetch(\PDO::FETCH_NUM);
        if (isset($row[0])) {
            try {
                return unserialize($row[0]);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function exists($key)
    {
        $prepared = $this->getPreparedGet();
        $prepared->bindValue('hash', $this->hash($key), \PDO::PARAM_STR);
        $prepared->bindValue('expiration', time(), \PDO::PARAM_INT);
        $prepared->execute();

        $row = $prepared->fetch(\PDO::FETCH_NUM);
        if (isset($row[0])) {
            return true;
        }
        return false;
    }

    public function set($key, $value, $expiration = 0)
    {
        if ($expiration != 0 && $expiration <= 30 * 86400) {
            $expiration += time();
        }

        $prepared = $this->getPreparedSet();
        $prepared->bindValue('hash', $this->hash($key), \PDO::PARAM_STR);
        $prepared->bindValue('value', serialize($value), \PDO::PARAM_LOB);
        $prepared->bindValue('expiration', $expiration, \PDO::PARAM_INT);
        $prepared->execute();
    }

    public function remove($key)
    {
        $prepared = $this->getPreparedRemove();
        $prepared->bindValue('hash', $this->hash($key), \PDO::PARAM_STR);
        $prepared->execute();
    }

    /**
     *
     * @return \PDOStatement
     */
    protected function getPreparedRemove()
    {
        if (empty($this->prepared['remove'])) {
            $sql                      = 'DELETE FROM `' . $this->table . '` WHERE `hash`=:hash';
            $this->prepared['remove'] = $this->pdo->prepare($sql);
        }
        return $this->prepared['remove'];
    }

    /**
     *
     * @return \PDOStatement
     */
    protected function getPreparedGet()
    {
        if (empty($this->prepared['get'])) {
            $sql                   = 'SELECT `value` FROM `' . $this->table . '` WHERE `hash`=:hash and `expiration`>=:expiration';
            $this->prepared['get'] = $this->pdo->prepare($sql);
        }
        return $this->prepared['get'];
    }

    /**
     *
     * @return \PDOStatement
     */
    protected function getPreparedSet()
    {
        if (!isset($this->prepared['set'])) {
            $sql                   = 'REPLACE INTO `' . $this->table . '` (`hash`, `value`, `expiration` ) VALUES (:hash, :value, :expiration)';
            $this->prepared['set'] = $this->pdo->prepare($sql);
        }
        return $this->prepared['set'];
    }
}
