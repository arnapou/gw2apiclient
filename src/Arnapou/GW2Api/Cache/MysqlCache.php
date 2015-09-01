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

use \FilesystemIterator;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use Arnapou\GW2Api\Exception\Exception;

class MysqlCache implements CacheInterface {

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
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo, $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Ran when php exits : automatically run of GC if conditions are met
     */
    public function __destruct() {
        $rand = mt_rand(1, $this->gcDivisor);
        if ($rand <= $this->gcProbability) {
            $this->runGarbageCollector();
        }
    }

    /**
     * Run the garbage collector which clean expired files
     */
    public function runGarbageCollector() {
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
    public function setGarbageCollectorParameters($gcProbability, $gcDivisor) {
        if ($gcDivisor < 1) {
            throw new Exception('gcDivisor should be strictly > 0.');
        }
        if ($gcDivisor < 0) {
            throw new Exception('gcProbability cannot be negative.');
        }
        $this->gcDivisor = $gcDivisor;
        $this->gcProbability = $gcProbability;
    }

    public function get($key) {
        $hash = hash('sha256', $key);
        $sql = "SELECT `value` FROM `" . $this->table . "` WHERE `hash`=" . $this->pdo->quote($hash);
        try {
            foreach ($this->pdo->query($sql, \PDO::FETCH_ASSOC) as $row) {
                return unserialize($row['value']);
            }
        }
        catch (\Exception $e) {
            
        }
        return null;
    }

    public function set($key, $value, $expiration = 0) {
        $hash = hash('sha256', $key);
        if ($expiration != 0 && $expiration <= 30 * 86400) {
            $expiration += time();
        }

        $sql = "REPLACE INTO `" . $this->table . "` (`hash`, `value`, `expiration` ) VALUES "
            . "(" . $this->pdo->quote($hash) . "," . $this->pdo->quote(serialize($value)) . "," . $this->pdo->quote($expiration) . ")";
        $this->pdo->exec($sql);
    }

    public function exists($key) {
        $hash = hash('sha256', $key);
        $sql = "SELECT COUNT(`key`) as nb FROM `" . $this->table . "`  WHERE `hash`=" . $this->pdo->quote($hash);
        foreach ($this->pdo->query($sql, \PDO::FETCH_ASSOC) as $row) {
            return $row['nb'] == 1 ? true : false;
        }
        return false;
    }

    public function remove($key) {
        $hash = hash('sha256', $key);
        $sql = "DELETE FROM `" . $this->table . "` WHERE `hash`=" . $this->pdo->quote($hash);
        $this->pdo->exec($sql);
    }

}
