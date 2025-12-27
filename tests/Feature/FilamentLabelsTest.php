<?php

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Filament\Resources\CannedResponses\CannedResponseResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Departments\DepartmentResource;
use App\Filament\Resources\Tickets\TicketResource;

test('department resource has turkish labels', function () {
    expect(DepartmentResource::getModelLabel())->toBe('Bölüm');
    expect(DepartmentResource::getPluralModelLabel())->toBe('Bölümler');
    expect(DepartmentResource::getNavigationLabel())->toBe('Bölümler');
});

test('category resource has turkish labels', function () {
    expect(CategoryResource::getModelLabel())->toBe('Kategori');
    expect(CategoryResource::getPluralModelLabel())->toBe('Kategoriler');
    expect(CategoryResource::getNavigationLabel())->toBe('Kategoriler');
});

test('ticket resource has turkish labels', function () {
    expect(TicketResource::getModelLabel())->toBe('Talep');
    expect(TicketResource::getPluralModelLabel())->toBe('Talepler');
    expect(TicketResource::getNavigationLabel())->toBe('Talepler');
});

test('announcement resource has turkish labels', function () {
    expect(AnnouncementResource::getModelLabel())->toBe('Duyuru');
    expect(AnnouncementResource::getPluralModelLabel())->toBe('Duyurular');
    expect(AnnouncementResource::getNavigationLabel())->toBe('Duyurular');
});

test('canned response resource has turkish labels', function () {
    expect(CannedResponseResource::getModelLabel())->toBe('Hazır Cevap');
    expect(CannedResponseResource::getPluralModelLabel())->toBe('Hazır Cevaplar');
    expect(CannedResponseResource::getNavigationLabel())->toBe('Hazır Cevaplar');
});
