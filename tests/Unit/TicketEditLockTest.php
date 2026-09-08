<?php

use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketEditLock;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Facades\Facade;
use Illuminate\Validation\ValidationException;

test('ticket leases reject competing admins and stale tabs and allow release and expiry', function () {
    if (! in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        $this->markTestSkipped('This isolated lock test requires pdo_sqlite.');
    }
    $previousContainer = Container::getInstance();
    $previousFacade = Facade::getFacadeApplication();
    $previousResolver = Ticket::getConnectionResolver();
    $container = new Container();
    Container::setInstance($container);
    Facade::setFacadeApplication($container);
    Facade::clearResolvedInstances();
    $db = new Manager($container);
    $db->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
    $db->bootEloquent();
    $container->instance('db', $db->getDatabaseManager());
    $translator = new Illuminate\Translation\Translator(new Illuminate\Translation\ArrayLoader(), 'en');
    $container->instance('validator', new Illuminate\Validation\Factory($translator, $container));
    $actor = new class {
        public int $actorId = 1;
    };
    // Give the authenticated model its actual key for category visibility queries.
    $auth = new class($actor) implements Illuminate\Contracts\Auth\Factory {
        public function guard($name = null) { return $this; }
        public function shouldUse($name) {}
        public function __construct(public object $actor) {}
        public function id() { return $this->actor->actorId; }
        public function user() { $u = new User(['is_admin' => true]); $u->id = $this->id(); return $u; }
    };
    $container->instance('auth', $auth);
    $container->instance(Illuminate\Contracts\Auth\Factory::class, $auth);
    try {
        $connection = $db->getConnection();
        $connection->statement('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
        $connection->statement('CREATE TABLE categories (id INTEGER PRIMARY KEY)');
        $connection->statement('CREATE TABLE category_user (user_id INTEGER, category_id INTEGER)');
        $connection->statement('CREATE TABLE tickets (id INTEGER PRIMARY KEY, category_id INTEGER, edit_lock_user_id INTEGER, edit_lock_token TEXT, edit_lock_expires_at TEXT)');
        $connection->table('users')->insert([['id' => 1, 'name' => 'Birinci Admin'], ['id' => 2, 'name' => 'İkinci Admin']]);
        $connection->table('tickets')->insert(['id' => 1]);
        $service = new TicketEditLock();
        $service->acquire(1, 'tab-one');
        expect(fn () => $service->acquire(1, 'another-tab'))->toThrow(ValidationException::class);
        $actor->actorId = 2;
        expect(fn () => $service->acquire(1, 'tab-two'))->toThrow(ValidationException::class);
        expect(fn () => $service->run(1, null, fn () => true))->toThrow(ValidationException::class);
        $service->release(1, 'tab-one');
        expect($connection->table('tickets')->value('edit_lock_token'))->toBe('tab-one');
        $actor->actorId = 1;
        expect($service->run(1, 'tab-one', fn () => 'saved'))->toBe('saved');
        $service->release(1, 'tab-one');
        $actor->actorId = 2;
        $service->acquire(1, 'tab-two');
        $connection->table('tickets')->update(['edit_lock_expires_at' => now()->subSecond()->toDateTimeString()]);
        expect(fn () => $service->run(1, 'tab-two', fn () => true))->toThrow(ValidationException::class);
        $actor->actorId = 1;
        $service->acquire(1, 'tab-three');
        expect($connection->table('tickets')->value('edit_lock_token'))->toBe('tab-three');
    } finally {
        if ($previousResolver) Ticket::setConnectionResolver($previousResolver);
        else Ticket::unsetConnectionResolver();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($previousFacade);
        Container::setInstance($previousContainer);
    }
});
