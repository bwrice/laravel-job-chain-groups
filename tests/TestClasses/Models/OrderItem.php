<?php


namespace Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models;


use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderItem
 * @package Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models
 *
 * @property int $id
 * @property string $uuid
 * @property CarbonInterface $processed_at
 * @property Order $order
 */
class OrderItem extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
