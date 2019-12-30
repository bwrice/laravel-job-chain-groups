<?php


namespace Bwrice\LaravelJobChainGroups\Models;


use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChildJob
 * @package Bwrice\LaravelJobChainGroups\models
 *
 * @property string $uuid
 * @property string $group_uuid
 * @property CarbonInterface $processed_at
 *
 * @method static Builder unprocessedForGroup(string $groupUuid)
 */
class ChainGroupMember extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    protected $guarded = [];
    protected $dates = ['processed_at'];

    public $table = 'chain_group_members';

    public function scopeUnprocessedForGroup(Builder $builder, string $groupUuid)
    {
        return $builder->where('group_uuid', '=', $groupUuid)->whereNull('processed_at');
    }
}
