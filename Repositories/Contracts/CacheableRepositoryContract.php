<?php
namespace Laracraft\Core\Repositories\Contracts;

use Illuminate\Contracts\Cache\Repository as CacheRepository;


interface CacheableRepositoryContract extends RepositoryContract{

	/**
	 * Set the Cache repository
	 *
	 * @param CacheRepository $repository
	 *
	 * @return mixed
	 */
	public function setCacheRepository(CacheRepository $repository);

	/**
	 * Return instance of Cache Repository
	 *
	 * @return CacheRepository
	 */
	public function getCacheRepository();

	/**
	 * Skip Cache
	 *
	 * @param bool $skip
	 *
	 * @return $this
	 */
	public function skipCache($skip = true);

	/**
	 * Is the cache being skipped
	 *
	 * @return bool
	 */
	public function isSkippedCache();


	public function flushCache($tags = []);

	public function getCacheTags();

}