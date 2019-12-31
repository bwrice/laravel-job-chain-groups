<?php


namespace Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models;


use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * @package Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models
 *
 * @property int $id
 * @property CarbonInterface $preprocessed_at
 * @property CarbonInterface $shipped_at
 *
 * @property Collection $orderItems
 */
class Order extends Model
{
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
