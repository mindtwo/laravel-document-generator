<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldGenerateEvent;
use Tests\Fake\Modules\Document\TestDocument;

it('registers listners for the document events', function () {
    Event::fake();

    Event::assertListening(
        \mindtwo\DocumentGenerator\Modules\Document\Events\DocumentGeneratedEvent::class,
        \mindtwo\DocumentGenerator\Modules\Content\Listeners\DocumentGeneratedListener::class,
    );

    Event::assertListening(
        \mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldSaveToDiskEvent::class,
        \mindtwo\DocumentGenerator\Modules\Generation\Listeners\DocumentShouldSaveListener::class
    );
});

it('generates the content when the document is created', function () {
    $model = \Tests\Fake\Models\TestModel::create([
        'id' => 1,
        'title' => 'Test Title',
    ]);

    $doc = new TestDocument($model);
    $generated = $doc->generate();

    expect($generated->content)->not->toBeNull();
});

it('updates the document instance and generates a file', function () {
    $storage = Storage::fake('local');

    Event::fake([
        DocumentShouldGenerateEvent::class,
        \mindtwo\DocumentGenerator\Modules\Document\Events\DocumentSavedToDiskEvent::class,
    ]);

    $model = \Tests\Fake\Models\TestModel::create([
        'id' => 1,
        'title' => 'Test Title',
    ]);

    $doc = new TestDocument($model);
    $generated = $doc->generate();

    Event::assertDispatched(DocumentShouldGenerateEvent::class);

    $generated->saveToDisk();
    Event::assertDispatched(\mindtwo\DocumentGenerator\Modules\Document\Events\DocumentSavedToDiskEvent::class);

    $storage->assertExists($generated->file_path);
});
