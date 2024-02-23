<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Listeners;

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
        // TODO content
    }
}
