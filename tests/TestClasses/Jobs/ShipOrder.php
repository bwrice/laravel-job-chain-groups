<?php


namespace Bwrice\LaravelJobChainGroups\Tests\TestClasses\Jobs;


use Bwrice\LaravelJobChainGroups\Tests\TestClasses\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

class ShipOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        $this->order->shipped_at = Date::now();
        $this->order->save();
    }
}
