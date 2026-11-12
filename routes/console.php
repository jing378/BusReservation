<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('bus:maintenance')->everyFiveMinutes()->withoutOverlapping();
