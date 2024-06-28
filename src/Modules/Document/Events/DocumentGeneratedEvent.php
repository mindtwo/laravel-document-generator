<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

class DocumentGeneratedEvent
{

    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public GeneratedDocument $document,
    ) {
    }
}
