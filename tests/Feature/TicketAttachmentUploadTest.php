<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketAttachmentUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_files_with_ticket(): void
    {
        Storage::fake('local');
        $this->withoutMiddleware();

        $category = Category::create(['name' => 'Test Category']);
        
        $file1 = UploadedFile::fake()->image('error.jpg');
        $file2 = UploadedFile::fake()->create('log.txt', 100);

        $response = $this->post(route('tickets.store'), [
            'name' => 'John Doe',
            'department_room' => '101',
            'phone_number' => '1234',
            'category_id' => $category->id,
            'subject' => 'Help',
            'description' => 'Error occurred',
            'attachments' => [$file1, $file2],
        ]);

        $response->assertSessionHas('success');

        $ticket = Ticket::first();
        $this->assertNotNull($ticket);
        $this->assertCount(2, $ticket->attachments);

        $this->assertDatabaseHas('ticket_attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'error.jpg',
        ]);

        $this->assertDatabaseHas('ticket_attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'log.txt',
        ]);

        Storage::disk('local')->assertExists($ticket->attachments->first()->file_path);
        Storage::disk('local')->assertExists($ticket->attachments->last()->file_path);
    }

    public function test_validation_fails_for_invalid_file_type(): void
    {
        Storage::fake('local');
        $this->withoutMiddleware();
        $category = Category::create(['name' => 'Test Category']);
        
        $file = UploadedFile::fake()->create('malicious.exe', 100);

        $response = $this->post(route('tickets.store'), [
            'name' => 'John Doe',
            'department_room' => '101',
            'phone_number' => '1234',
            'category_id' => $category->id,
            'subject' => 'Help',
            'description' => 'Error occurred',
            'attachments' => [$file],
        ]);

        $response->assertSessionHasErrors(['attachments.0']);
    }
}
