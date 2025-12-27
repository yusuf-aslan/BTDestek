<?php

use Illuminate\Support\Facades\Config;

test('application locale is turkish', function () {
    expect(Config::get('app.locale'))->toBe('tr');
});

test('application timezone is Istanbul', function () {
    expect(Config::get('app.timezone'))->toBe('Europe/Istanbul');
});

test('fallback locale is turkish', function () {
    expect(Config::get('app.fallback_locale'))->toBe('tr');
});