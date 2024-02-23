<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Listeners;

use mindtwo\DocumentGenerator\Modules\Content\Services\DocumentContent;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldGenerateEvent;

class DocumentShouldGenerateListener
{
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(DocumentShouldGenerateEvent $event): void
    {
        $generatedDocument = $event->document;

        if ($generatedDocument->content) {
            return;
        }

        $documentContent = new DocumentContent(
            $generatedDocument->instance,
            $generatedDocument->model
        );

        $generatedDocument->update([
            'content' => $documentContent->html(),
        ]);
    }


}
