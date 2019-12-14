<?php


namespace Bwrice\LaravelJobChainGroups\models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class ChildJob
 * @package Bwrice\LaravelJobChainGroups\models
 *
 * @property ChainGroup $parent
 */
class ChainGroupMember extends Model
{
    public function parent()
    {
        return $this->belongsTo(ChainGroup::class);
    }
}
