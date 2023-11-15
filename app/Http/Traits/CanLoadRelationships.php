<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
trait CanLoadRelationships
{
    public function LoadRelationships(
        Model|QueryBuilder|EloquentBuilder|HasMany $for,
        ?array $relations = null
    ): Model|QueryBuilder|EloquentBuilder|HasMany{
        $relations = $relations ?? $this->relations ?? [];

        foreach($relations as $relation){
            $for->when(
                $this->shouldIncludeRelation($relation),
                fn($q) =>  $for instanceof Model ? $for->load($relation) : $q->with($relation)
            );
        }
        return $for;
    }
    protected function shouldIncludeRelation(string $relation): bool{
        $include = request()->query("include");
        if(!$include):
            return false;
        endif;
        $relations = array_map('trim',preg_split("/[,!]/",$include)); // preg_split is the explode alt

        return in_array($relation,$relations);
    }
}