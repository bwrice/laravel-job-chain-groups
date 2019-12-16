<?php


namespace Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models;


use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * @package Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models
 *
 * @property int $id
 * @property string $uuid
 * @property CarbonInterface $preprocessed_at
 * @property CarbonInterface $shipped_at
 */
class Order extends Model
{

}
