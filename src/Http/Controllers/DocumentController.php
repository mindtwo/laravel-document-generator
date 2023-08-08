<?php

namespace mindtwo\DocumentGenerator\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use mindtwo\DocumentGenerator\Actions\DeleteDocumentAction;
use mindtwo\DocumentGenerator\Actions\DownloadDocumentAction;
use mindtwo\DocumentGenerator\Actions\UpdateLayoutAction;
use mindtwo\DocumentGenerator\Http\Requests\EditLayoutRequest;
use mindtwo\DocumentGenerator\Models\DocumentLayout;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Services\DocumentEditor;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends BaseController
{
    public function __construct(
        protected DocumentEditor $documentEditor,
        protected UpdateLayoutAction $updateLayoutAction,
        protected DeleteDocumentAction $deleteDocumentAction,
    ) {
    }

    /**
     * Show the page for editing the specified resource.
     *
     * @param  string|int  $layoutIdentifier - integer id or uuid string
     * @return JsonResponse
     */
    public function show(string|int $layoutIdentifier): JsonResponse
    {
        $documentLayout = DocumentLayout::query()->byIdentifier($layoutIdentifier)->firstOrFail();

        if (! Gate::allows('update-document-layout', $documentLayout)) {
            abort(401);
        }

        /** @var EditDocument $editDocument */
        $editDocument = $this->documentEditor->loadLayout($layoutIdentifier);

        return response()->json([
            'editDocument' => $editDocument->toJson(),
        ]);
    }

    /**
     * Update a document layout.
     *
     * @param  string|int  $layoutIdentifier
     * @param  EditLayoutRequest  $editLayoutRequest
     * @return JsonResponse
     */
    public function update(string|int $layoutIdentifier, EditLayoutRequest $editLayoutRequest): JsonResponse
    {
        $documentLayout = DocumentLayout::query()->byIdentifier($layoutIdentifier)->first();
        if (! $editLayoutRequest->user()->can('update-document-layout', $documentLayout)) {
            abort(401);
        }

        $this->updateLayoutAction->execute($documentLayout, $editLayoutRequest);

        return response()->json([
            'message' => __('documents.update.success'),
            'data' => $editLayoutRequest->validated(),
        ]);
    }

    /**
     * Download document by uuid.
     *
     * @param  string  $documentId
     * @param DownloadDocumentAction $downloadDocumentAction
     * @return StreamedResponse
     */
    public function download(string $documentId, DownloadDocumentAction $downloadDocumentAction): StreamedResponse
    {
        $generatedDocument = GeneratedDocument::where('uuid', $documentId)->first();

        if (is_null($generatedDocument) || Gate::denies('download-document', $generatedDocument)) {
            abort(401);
        }

        if (is_null($generatedDocument)) {
            abort(404);
        }

        try {
            return ($downloadDocumentAction)($generatedDocument);
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'documentId' => $documentId,
                'generatedDocument' => $generatedDocument,
                'trace' => $th->getTrace(),
            ]);

            abort(500);
        }
    }

    /**
     * Download tmp document by it's name and delete it afterwards.
     *
     * @param  string  $documentId
     * @param DownloadDocumentAction $downloadDocumentAction
     * @return StreamedResponse
     */
    public function getTmp(string $fileName, DownloadDocumentAction $downloadDocumentAction): StreamedResponse
    {
        if (! Gate::allows('create-tmp-document')) {
            abort(401);
        }

        $tmpPath = config('documents.files.tmp') ?? '/tmp/documents';

        // dispatch closure to delete tmpFile after 1 minute
        dispatch(fn () => $this->deleteDocumentAction->execute($fileName, $tmpPath))
            ->delay(now()->addMinute())
            ->catch(function (\Throwable $e) use ($fileName, $tmpPath) {
                Log::warning("Couldn't find tmpFile {$tmpPath}/{$fileName} skipping...");
            });

        return ($downloadDocumentAction)->execute($fileName, $tmpPath);
    }
}
