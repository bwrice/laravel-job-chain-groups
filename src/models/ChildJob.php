<?php


namespace Bwrice\LaravelJobChainGroups\models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class ChildJob
 * @package Bwrice\LaravelJobChainGroups\models
 *
 * @property ParentJob $parent
 */
class ChildJob extends Model
{
    public function parent()
    {
        return $this->belongsTo(ParentJob::class);
    }
}
