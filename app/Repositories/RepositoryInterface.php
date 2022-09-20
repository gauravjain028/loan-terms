<?php

declare(strict_types = 1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface RepositoryInterface
 *
 * @package App\Repositories
 */
interface RepositoryInterface
{
    /**
     * Create a new model record in the database.
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data) : ?Model;

    /**
     * Find single model by id.
     *
     * @param int  $id
     * @param bool $findOrFail
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find(int $id, bool $findOrFail = true) : ?Model;

    /**
     * Execute the query and get the first result.
     *
     * @param array|string[] $columns
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function first(array $columns = ['*']) : ?Model;

    /**
     * Find single model by id with given relations.
     *
     * @param int   $id
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findWith(int $id, array $relations) : Model;

    /**
     * Find all models.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll() : Collection;

    /**
     * Find all models with given relations.
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllWith(array $relations) : Collection;

    /**
     * Find by specified criteria.
     *
     *
     * @param array<int, array<int, mixed>> $criteria
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findBy(array $criteria) : Collection;

    /**
     * Find many models with given criteria and relations.
     *
     * @param array $criteria
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByWith(array $criteria, array $relations) : Collection;

    /**
     * Find single model bt given criteria.
     *
     * @param array $criteria
     *
     * @param bool  $firstOrFail
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findOneBy(array $criteria, bool $firstOrFail = true) : ?Model;

    /**
     * Delete by specified criteria.
     *
     * @param array $criteria
     *
     * @return bool
     */
    public function deleteBy(array $criteria) : bool;

    /**
     * Updates existing entity or creates new one.
     *
     * @param array $data
     * @param array $newData
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate(array $data, array $newData) : Model;

    /**
     * Delete single model by id or returns model not found exception.
     *
     * @param int $id
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteOrFailById(int $id) : bool;

    /**
     * Find one item with given criteria and relations.
     *
     * @param array $criteria
     * @param array $relations
     * @param bool  $firstOrFail
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findOneByWith(array $criteria, array $relations, bool $firstOrFail = true) : ?Model;

    /**
     * Get model builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery() : Builder;

    /**
     * Check existence by given criteria.
     *
     * @param array $criteria
     *
     * @return bool
     */
    public function exists(array $criteria) : bool;
}
