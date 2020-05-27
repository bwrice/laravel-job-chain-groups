<?php


namespace Bwrice\LaravelJobChainGroups\Models;


use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChainGroup
 * @package Bwrice\LaravelJobChainGroups\models
 *
 * @property string $uuid
 * @property string $group_uuid
 * @property CarbonInterface $processed_at
 *
 * @property Collection $chainGroupMembers
 */
class ChainGroup extends Model
{
    protected $guarded = [];
    protected $dates = [
        'processed_at',
        'created_at',
        'updated_at'
    ];

    public $table = 'chain_groups';

    public function chainGroupMembers()
    {
        return $this->hasMany(ChainGroupMember::class);
    }
}
