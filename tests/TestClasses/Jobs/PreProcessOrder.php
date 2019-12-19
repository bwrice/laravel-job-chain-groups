<?php


namespace Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs;


use Bwrice\LaravelJobChainGroups\jobs\AsyncChainedJob;
use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Bwrice\LaravelJobChainGroups\traits\AsyncChainable;
use Illuminate\Support\Facades\Date;

class PreProcessOrder
{
    use AsyncChainable;

    /** @var Order */
    public  $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        $this->order->preprocessed_at = Date::now();
        $this->order->save();
    }
}
