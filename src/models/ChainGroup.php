<?php


namespace Bwrice\LaravelJobChainGroups\models;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParentJob
 * @package Bwrice\LaravelJobChainGroups\models
 *
 * @property Collection $children
 */
class ChainGroup extends Model
{
    public function children()
    {
        return $this->hasMany(ChainGroupMember::class);
    }
}
