<?php

/*
 * This file is part of the Arnapou GW2 API Client package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arnapou\GW2Api\Cache;

use \FilesystemIterator;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use Arnapou\GW2Api\Exception\Exception;

class FileCache implements CacheInterface {

	/**
	 *
	 * @var string
	 */
	protected $cachePath = array();

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
	 * @var bool
	 */
	protected $opcacheInvalidateExists;

	/**
	 * 
	 * @param string $path
	 */
	public function __construct($path) {
		if (!is_dir($path)) {
			throw new Exception("The FileCache path does not exists.");
		}
		if (!is_writable($path)) {
			throw new Exception("The FileCache path is not writable.");
		}
		$this->cachePath = rtrim(rtrim($path, '\\'), '/');
		$this->opcacheInvalidateExists = function_exists('opcache_invalidate');
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
		$flags = FilesystemIterator::KEY_AS_PATHNAME;
		$flags|= FilesystemIterator::SKIP_DOTS;
		$flags|= FilesystemIterator::CURRENT_AS_FILEINFO;
		$directoryIterator = new RecursiveDirectoryIterator($this->cachePath, $flags);

		$flags = RecursiveIteratorIterator::LEAVES_ONLY;
		$iterator = new RecursiveIteratorIterator($directoryIterator, $flags);
		$time = time();
		foreach ($iterator as /* @var $file \SplFileInfo */ $file) {
			if ($file->getExtension() == 'php') {
				include $file->getPathname();
				if ($expires != 0 && $expires < $time) {
					@unlink($file->getPathname());
				}
			}
		}
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

	/**
	 * 
	 * @param string $key
	 * @return type
	 */
	public function getFilename($key) {
		$hash = hash('sha256', $key);
		return $this->cachePath . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . substr($hash, 4) . '.php';
	}

	public function get($key) {
		if ($this->exists($key)) {
			$filename = $this->getFilename($key);
			include $filename;
			if (isset($data)) {
				return $data;
			}
		}
		return null;
	}

	public function set($key, $value, $expiration = 0) {
		if ($expiration != 0 && $expiration <= 30 * 86400) {
			$expiration += time();
		}
		$filename = $this->getFilename($key);
		$content = "<?php\n";
		$content.= "/* key = $key */\n";
		$content.= "\$expires = $expiration;" . ($expiration != 0 ? " // " . date('Y-m-d H:i:s', $expiration) : "") . "\n";
		$content.= "\$data = " . var_export($value, true) . ";\n";
		$this->directoryCreateIfNotExists(dirname($filename));
		file_put_contents($filename, $content, LOCK_EX);
		if ($this->opcacheInvalidateExists) {
			opcache_invalidate($filename, true);
			@touch($filename);
		}
	}

	/**
	 * 
	 * @param string $path
	 */
	protected function directoryCreateIfNotExists($path) {
		if (!empty($path) && !is_dir($path)) {
			$dir = dirname($path);
			$this->directoryCreateIfNotExists($dir);
			if (!is_writable($dir)) {
				throw new Exception('Directory "' . $dir . '" is not writable.');
			}
			mkdir($path, 0777);
			chmod($path, 0777);
		}
	}

	public function exists($key) {
		$filename = $this->getFilename($key);
		if (is_file($filename)) {
			include $filename;
			if ($expires == 0 || $expires > time()) {
				return true;
			}
			@unlink($filename);
		}
		return false;
	}

	public function remove($key) {
		$filename = $this->getFilename($key);
		if (is_file($filename)) {
			@unlink($filename);
		}
	}

}
