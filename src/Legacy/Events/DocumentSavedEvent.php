<?php

namespace mindtwo\DocumentGenerator\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;

class DocumentSavedEvent
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
        public Model $model,
    ) {
    }
}
