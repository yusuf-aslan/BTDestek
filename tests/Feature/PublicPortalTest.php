<?php

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

test('landing page displays active announcements', function () {
    Announcement::create([
        'title' => 'Maintenance Alert',
        'content' => 'System will be down',
        'type' => 'warning',
        'is_active' => true
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('Maintenance Alert')
        ->assertSee('System will be down');
});

test('hospital staff can submit a ticket from the landing page', function () {
    $category = Category::create(['name' => 'Hardware']);

    $this->withoutMiddleware()
        ->post('/talep-olustur', [
            'name' => 'Ayşe Yılmaz',
            'department_room' => 'Poliklinik 1',
            'category_id' => $category->id,
            'subject' => 'Yazıcı çalışmıyor',
            'description' => 'Yazıcıdan ses geliyor ama çıktı vermiyor.',
        ])->assertRedirect();

    expect(Ticket::where('name', 'Ayşe Yılmaz')->exists())->toBeTrue();
    $ticket = Ticket::where('name', 'Ayşe Yılmaz')->first();
    expect($ticket->tracking_number)->not->toBeNull();
});

test('hospital staff can track their ticket status', function () {
    $category = Category::create(['name' => 'Software']);
    $ticket = Ticket::create([
        'tracking_number' => 'BT-TEST-999',
        'name' => 'Mehmet Öz',
        'department_room' => 'Muhasebe',
        'category_id' => $category->id,
        'subject' => 'HBYS Giriş',
        'description' => 'Şifremi unuttum.',
        'status' => 'işlemde',
        'priority' => 'orta'
    ]);

    $this->get("/talep-sorgula?tracking_number=BT-TEST-999")
        ->assertSuccessful()
        ->assertSee('BT-TEST-999')
        ->assertSee('Mehmet Öz')
        ->assertSee('işlemde'); 
});

test('tracking with invalid number shows error', function () {
    $this->get("/talep-sorgula?tracking_number=INVALID-123")
        ->assertRedirect()
        ->assertSessionHasErrors(['tracking_number']);
});
