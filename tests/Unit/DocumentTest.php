<?php


it('can generate a document with custom file path and file name generator', function () {
    $model = new \Tests\Fake\Models\TestModel([
        'id' => 1,
        'title' => 'Test Title',
    ]);

    $document = \Tests\Fake\Modules\Document\TestDocument::make($model);

    $this->assertEquals('test_file_name_1', $document->fileName());

    $generatedUuid = $document->getGeneratedDocument()->uuid;
    $this->assertEquals(strtolower(substr($generatedUuid, 0, 2)) . '/' . strtolower(substr($generatedUuid, 2, 2)), $document->filePath());
});
