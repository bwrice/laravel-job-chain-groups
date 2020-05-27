<?php


namespace Bwrice\LaravelJobChainGroups\Models;


use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChainGroupMember
 * @package Bwrice\LaravelJobChainGroups\models
 *
 * @property int $id
 * @property int $chain_group_id
 * @property CarbonInterface $processed_at
 *
 * @method static Builder unprocessedForGroup(string $groupUuid)
 */
class ChainGroupMember extends Model
{
    protected $guarded = [];
    protected $dates = [
        'processed_at',
        'created_at',
        'updated_at'
    ];

    public $table = 'chain_group_members';

    public function scopeUnprocessedForGroup(Builder $builder, string $groupUuid)
    {
        return $builder->where('group_uuid', '=', $groupUuid)->whereNull('processed_at');
    }
}
