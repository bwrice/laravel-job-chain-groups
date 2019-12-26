<?php


namespace Bwrice\LaravelJobChainGroups\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChildJob
 * @package Bwrice\LaravelJobChainGroups\models
 *
 * @property int $id
 * @property string $group_uuid
 *
 * @method static Builder groupUuid(string $groupUuid)
 */
class ChainGroupMember extends Model
{
    protected $primaryKey = 'uuid';
    protected $guarded = [];

    public $table = 'chain_group_members';

    public function scopeGroupUuid(Builder $builder, string $groupUuid)
    {
        return $builder->where('group_uuid', '=', $groupUuid);
    }
}
