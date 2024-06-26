<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use mindtwo\DocumentGenerator\Modules\Document\Document;

class DocumentShouldGenerateEvent
{

    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Document $instance,
    ) {
    }
}
