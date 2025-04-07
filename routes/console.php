<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Order;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('schedule:run', function () use ($schedule) {
    $schedule->call(function () {
        // Đánh dấu đơn hàng hết hạn nếu không có callback trong 5 phút
        $expiredOrders = Order::where('paymentMethod', 'BANKING')
            ->where('payment_status', 'Pending')
            ->where('created_at', '<=', now()->subMinutes(5))
            ->get();

        foreach ($expiredOrders as $order) {
            $order->payment_status = 'Expired'; // Đánh dấu trạng thái là 'Expired'
            $order->save();
        }
    })->everyFiveMinutes(); // Chạy mỗi 5 phút
});