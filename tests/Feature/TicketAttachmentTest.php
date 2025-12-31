<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TicketAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_attachments_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('ticket_attachments'));
        $this->assertTrue(Schema::hasColumns('ticket_attachments', [
            'id',
            'ticket_id',
            'file_path',
            'file_name',
            'file_type',
            'file_size',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_ticket_attachment_model_can_be_created(): void
    {
        $category = \App\Models\Category::create(['name' => 'Test Category']);
        $ticket = Ticket::create([
            'tracking_number' => 'TEST-123',
            'name' => 'John Doe',
            'department_room' => '101',
            'phone_number' => '1234',
            'category_id' => $category->id,
            'subject' => 'Test Ticket',
            'description' => 'Test Description',
            'status' => 'yeni',
            'priority' => 'orta',
        ]);

        $attachment = TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'file_path' => 'attachments/test.jpg',
            'file_name' => 'test.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 1024,
        ]);

        $this->assertDatabaseHas('ticket_attachments', [
            'id' => $attachment->id,
            'ticket_id' => $ticket->id,
            'file_name' => 'test.jpg',
        ]);
    }

    public function test_ticket_has_many_attachments(): void
    {
        $category = \App\Models\Category::create(['name' => 'Test Category']);
        $ticket = Ticket::create([
            'tracking_number' => 'TEST-123',
            'name' => 'John Doe',
            'department_room' => '101',
            'phone_number' => '1234',
            'category_id' => $category->id,
            'subject' => 'Test Ticket',
            'description' => 'Test Description',
            'status' => 'yeni',
            'priority' => 'orta',
        ]);
        
        $attachment = TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'file_path' => 'attachments/test.jpg',
            'file_name' => 'test.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 1024,
        ]);

        $this->assertTrue($ticket->attachments->contains($attachment));
        $this->assertInstanceOf(TicketAttachment::class, $ticket->attachments->first());
    }

    public function test_attachment_belongs_to_ticket(): void
    {
        $category = \App\Models\Category::create(['name' => 'Test Category']);
        $ticket = Ticket::create([
            'tracking_number' => 'TEST-123',
            'name' => 'John Doe',
            'department_room' => '101',
            'phone_number' => '1234',
            'category_id' => $category->id,
            'subject' => 'Test Ticket',
            'description' => 'Test Description',
            'status' => 'yeni',
            'priority' => 'orta',
        ]);

        $attachment = TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'file_path' => 'attachments/test.jpg',
            'file_name' => 'test.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 1024,
        ]);

        $this->assertInstanceOf(Ticket::class, $attachment->ticket);
        $this->assertEquals($ticket->id, $attachment->ticket->id);
    }
}
