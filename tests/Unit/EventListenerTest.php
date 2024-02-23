<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Fake\Modules\Document\TestDocument;

it('registers listners for the document events', function () {
    Event::fake();

    Event::assertListening(
        \mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldGenerateEvent::class,
        \mindtwo\DocumentGenerator\Modules\Content\Listeners\DocumentShouldGenerateListener::class,
    );

    Event::assertListening(
        \mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldSaveToDiskEvent::class,
        \mindtwo\DocumentGenerator\Modules\Generation\Listeners\DocumentShouldSaveListener::class
    );
});

it('updates the document instance and generates a file', function () {
    $storage = Storage::fake('local');

    $model = \Tests\Fake\Models\TestModel::create([
        'id' => 1,
        'title' => 'Test Title',
    ]);

    $doc = new TestDocument($model);
    $generated = $doc->generate();

    $generated->saveToDisk();

    $storage->assertExists($generated->file_path);
});
