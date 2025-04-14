<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldSaveToDiskEvent;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

it('generates uuid when created', function () {
    $document = GeneratedDocument::create([
        'documentable_type' => 'test',
        'documentable_id' => 1,
        'document_class' => 'test',
    ]);

    expect($document->uuid)->not->toBeNull();
});

it('checks if document has content', function () {
    $document = GeneratedDocument::create([
        'content' => 'Some content',
        'documentable_type' => 'test',
        'documentable_id' => 1,
        'document_class' => 'test',
    ]);

    expect($document->hasContent)->toBeTrue();
});

it('checks if document is saved to disk', function () {
    $document = GeneratedDocument::create([
        'disk' => 'local',
        'file_path' => '/path/to/file',
        'file_name' => 'file.txt',
        'documentable_type' => 'test',
        'documentable_id' => 1,
        'document_class' => 'test',
    ]);

    expect($document->isSavedToDisk)->toBeTrue();
});

it('saves document to disk', function () {
    Event::fake(DocumentShouldSaveToDiskEvent::class);

    $document = GeneratedDocument::create([
        'documentable_type' => 'test',
        'documentable_id' => 1,
        'document_class' => 'test',
    ]);

    $document->saveToDisk();

    Event::assertDispatched(DocumentShouldSaveToDiskEvent::class);
});

it('downloads the document', function () {
    Storage::fake('local');

    Storage::disk('local')->put('path/to/file/file.txt', 'Some content');

    $document = GeneratedDocument::create([
        'documentable_type' => 'test',
        'documentable_id' => 1,
        'document_class' => 'test',
        'content' => 'Some content',
        'disk' => 'local',
        'file_path' => '/path/to/file',
        'file_name' => 'file.txt',
    ]);

    $fakeFile = $document->full_path;
    expect(Storage::disk('local')->exists($fakeFile))->toBeTrue();

    $response = $document->download();

    expect($response->headers->get('content-disposition'))->toContain('file.txt');
    // TODO better test download?
});

it('slugifies the downloads filename', function () {
    Storage::fake('local');

    Storage::disk('local')->put('path/to/file/äöü.txt', 'Some content');

    $document = GeneratedDocument::create([
        'documentable_type' => 'test',
        'documentable_id' => 1,
        'document_class' => 'test',
        'content' => 'Some content',
        'disk' => 'local',
        'file_path' => '/path/to/file',
        'file_name' => 'äöü.txt',
    ]);

    $fakeFile = $document->full_path;
    expect(Storage::disk('local')->exists($fakeFile))->toBeTrue();

    $response = $document->download();

    expect($response->headers->get('content-disposition'))->toContain('aou.txt');
});

it('can`t downloads the document without content', function () {
    Storage::fake('local');

    Storage::disk('local')->put('path/to/file/file.txt', 'Some content');

    $document = GeneratedDocument::create([
        'documentable_type' => 'test',
        'documentable_id' => 1,
        'document_class' => 'test',
        'disk' => 'local',
        'file_path' => '/path/to/file',
        'file_name' => 'file.txt',
    ]);

    $fakeFile = $document->full_path;
    expect(Storage::disk('local')->exists($fakeFile))->toBeTrue();

    $response = $document->download();

    expect($response->status())->toEqual(404);
});

it('deletes the document model and removes the file from disk', function () {
    Storage::fake('local');

    // Assuming a document is created and saved to disk
    $document = GeneratedDocument::create([
        'disk' => 'local',
        'file_path' => '/path/to/file',
        'file_name' => 'file.txt',
        'documentable_type' => 'test',
        'documentable_id' => 1,
        'document_class' => 'test',
    ]);

    $fakeFile = $document->full_path;
    Storage::disk('local')->put($fakeFile, 'Content of the file');

    // Ensure the file exists before deletion
    expect(Storage::disk('local')->exists($fakeFile))->toBeTrue();

    // Delete the document
    $document->delete();

    // Assert the document is removed from the database
    expect(GeneratedDocument::find($document->id))->toBeNull();

    // Assert the file is removed from the disk
    expect(Storage::disk('local')->exists($fakeFile))->toBeFalse();
});
