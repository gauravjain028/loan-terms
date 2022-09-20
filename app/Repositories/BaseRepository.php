<?php

declare(strict_types = 1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use function is_array;

class BaseRepository implements RepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new model record in the database.
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data) : ?Model
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Find single model by id.
     *
     * @param int  $id
     * @param bool $findOrFail
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find(int $id, bool $findOrFail = true) : ?Model
    {
        return $findOrFail ?
            $this->model->newQuery()->findOrFail($id)
            : $this->model->newQuery()->find($id);
    }

    /**
     * Execute the query and get the first result.
     *
     * @param array|string[] $columns
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function first(array $columns = ['*']) : ?Model
    {
        return $this->model->newQuery()->first($columns);
    }

    /**
     * Find single model by id with given relations.
     *
     * @param int   $id
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findWith(int $id, array $relations) : Model
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Find all models by applying statements on relation.
     * Allowed to eager load relations by passing relation names.
     *
     * @param array    $criteria
     * @param string   $relation
     * @param callable $closure
     * @param array    $eagerLoadRelations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllWhereHasRelationWith(
        array    $criteria,
        string   $relation,
        callable $closure,
        array    $eagerLoadRelations = []
    ) : Collection {
        $query = $this->applyCriteria($criteria);

        return $query->whereHas($relation, $closure)->with($eagerLoadRelations)->get();
    }

    /**
     * Find all models.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll() : Collection
    {
        return $this->model->all();
    }

    /**
     * Find all models with given relations.
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllWith(array $relations) : Collection
    {
        return $this->model->with($relations)->get();
    }

    /**
     * Find by specified criteria.
     *
     * @param array $criteria
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findBy(array $criteria) : Collection
    {
        $query = $this->applyCriteria($criteria);

        return $query->get();
    }

    /**
     * Find many models with given criteria and relations.
     *
     * @param array $criteria
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByWith(array $criteria, array $relations) : Collection
    {
        $query = $this->applyCriteria($criteria);
        $query->with($relations);

        return $query->get();
    }

    /**
     * Find single model bt given criteria.
     *
     * @param array $criteria
     *
     * @param bool  $firstOrFail
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findOneBy(array $criteria, bool $firstOrFail = true) : ?Model
    {
        $query = $this->applyCriteria($criteria);

        return $firstOrFail ? $query->firstOrFail() : $query->first();
    }

    /**
     * Find one item with given criteria and relations.
     *
     * @param array $criteria
     * @param array $relations
     * @param bool  $firstOrFail
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findOneByWith(array $criteria, array $relations, bool $firstOrFail = true) : ?Model
    {
        $query = $this->applyCriteria($criteria);
        $query->with($relations);

        return $firstOrFail ? $query->firstOrFail() : $query->first();
    }

    /**
     * Delete by specified criteria.
     *
     * @param array $criteria
     *
     * @return bool
     */
    public function deleteBy(array $criteria) : bool
    {
        $query = $this->applyCriteria($criteria);

        return (bool) $query->delete();
    }

    /**
     * Apply given criteria to eloquent builder.
     *
     * @param array                                                                                       $criteria
     * @param \Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Builder|null $query
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Builder
     */
    protected function applyCriteria(array $criteria, $query = null)
    {
        $query = $query ?? $this->model->newQuery();

        foreach ($criteria as $criterion) {
            if (is_array($criterion[1])) {
                $query->whereIn(...$criterion);
            } else {
                $query->where(...$criterion);
            }
        }

        return $query;
    }

    /**
     * Delete single model by id or returns model not found exception.
     *
     * @param int $id
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteOrFailById(int $id) : bool
    {
        return (bool) $this->model->query()->findOrFail($id)->delete();
    }

    /**
     * Update existing entity or creates new one.
     *
     * @param array $data
     * @param array $newData
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate(array $data, array $newData) : Model
    {
        return $this->model->query()->updateOrCreate($data, $newData);
    }

    /**
     * Get model builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery() : Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Check existence by given criteria.
     *
     * @param array $criteria
     *
     * @return bool
     */
    public function exists(array $criteria) : bool
    {
        $query = $this->applyCriteria($criteria);

        return $query->exists();
    }
}
