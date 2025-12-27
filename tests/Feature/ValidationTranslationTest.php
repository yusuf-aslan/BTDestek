<?php

use Illuminate\Support\Facades\Lang;

test('validation messages are in turkish', function () {
    $message = Lang::get('validation.required', ['attribute' => 'test']);
    
    // Standard Turkish translation often uses "alanı gereklidir" or "zorunludur"
    expect($message)->toMatch('/(gereklidir|zorunludur)/');
});

test('pagination links are in turkish', function () {
    $message = Lang::get('pagination.previous');
    expect($message)->toContain('Önceki');
});