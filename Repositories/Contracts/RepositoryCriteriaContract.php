<?php
namespace Laracraft\Core\Repositories\Contracts;

use Laracraft\Core\Repositories\Criteria\Contracts\CriteriaContract;
use Illuminate\Support\Collection;

interface RepositoryCriteriaContract
{

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return $this
     */
    public function pushCriteria($criteria);

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria);

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria();

    /**
     * Find data by Criteria
     *
     * @param CriteriaContract $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaContract $criteria);

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * Reset all Criteria
     *
     * @return $this
     */
    public function resetCriteria();

}
