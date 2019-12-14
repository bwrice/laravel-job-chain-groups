<?php


namespace Bwrice\LaravelJobChainGroups\models;


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
    protected $guarded = [];

    public $table = 'chain_group_members';
}
