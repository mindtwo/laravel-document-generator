<?php

namespace mindtwo\DocumentGenerator\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;

class DocumentCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public GeneratedDocument $document,
    ) {
    }
}
