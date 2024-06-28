<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Listeners;

use mindtwo\DocumentGenerator\Modules\Content\Services\DocumentContent;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentGeneratedEvent;

class DocumentGeneratedListener
{
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(DocumentGeneratedEvent $event): void
    {
        $generatedDocument = $event->document;

        if ($generatedDocument->has_content) {
            return;
        }

        $documentContent = new DocumentContent(
            $generatedDocument->instance,
            $generatedDocument->model,
            $generatedDocument->extra ?? []
        );

        list($resolved, $content) = $documentContent->html();

        $generatedDocument->update([
            'resolved_placeholder' => $resolved,
            'content' => $content,
        ]);
    }

}
