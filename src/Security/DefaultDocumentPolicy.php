<?php

namespace mindtwo\DocumentGenerator\Security;

use Illuminate\Contracts\Auth\Authenticatable;
use mindtwo\DocumentGenerator\Models\DocumentLayout;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;

class DefaultDocumentPolicy extends DocumentPolicy
{
    /**
     * Determine if a user is allowed to download a given document.
     *
     * @param  Authenticatable  $user
     * @return bool
     */
    public function download(Authenticatable $user, GeneratedDocument $generatedDocument): bool
    {
        return true;
    }

    /**
     * Determine if a user is allowed to generate a document.
     *
     * @param  Authenticatable  $user
     * @return bool
     */
    public function create(Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Determine if a user is allowed to edit a document's layout.
     *
     * @param  Authenticatable  $user
     * @param  DocumentLayout  $documentLayout
     * @return bool
     */
    public function editLayout(Authenticatable $user, DocumentLayout $documentLayout): bool
    {
        return true;
    }
}
