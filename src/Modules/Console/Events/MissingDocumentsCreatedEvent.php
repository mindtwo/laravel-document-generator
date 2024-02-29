<?php

namespace mindtwo\DocumentGenerator\Modules\Console\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

class MissingDocumentsCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Collection<int, GeneratedDocument>  $createdDocuments
     */
    public function __construct(
        public string $documentClass,
        public Collection $createdDocuments,
    ) {
        //
    }
}
