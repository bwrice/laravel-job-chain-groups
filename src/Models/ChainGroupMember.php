<?php


namespace Bwrice\LaravelJobChainGroups\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class ChildJob
 * @package Bwrice\LaravelJobChainGroups\models
 *
 * @property int $id
 * @property string $group_uuid
 */
class ChainGroupMember extends Model
{
    protected $primaryKey = 'uuid';
    protected $guarded = [];

    public $table = 'chain_group_members';
}
