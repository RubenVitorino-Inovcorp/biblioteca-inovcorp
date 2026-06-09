<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('loans:update-overdue')->dailyAt('00:05');
Schedule::command('loans:send-reminders')->dailyAt('08:00');
Schedule::command('carts:send-abandoned-cart-notifications')->everyFiveMinutes();
