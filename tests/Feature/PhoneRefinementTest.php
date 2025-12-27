<?php

use Illuminate\Support\Facades\Schema;

test('tickets table has phone_number and not computer_name', function () {
    expect(Schema::hasColumn('tickets', 'phone_number'))->toBeTrue();
    expect(Schema::hasColumn('tickets', 'computer_name'))->toBeFalse();
});
