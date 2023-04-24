<?php

namespace mindtwo\DocumentGenerator\Security;

use Illuminate\Contracts\Auth\Authenticatable;
use mindtwo\DocumentGenerator\Models\DocumentLayout;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;

abstract class DocumentPolicy
{
    /**
     * Determine if a user is allowed to download a given document.
     *
     * @param  Authenticatable  $user
     * @return bool
     */
    abstract public function download(Authenticatable $user, GeneratedDocument $generatedDocument): bool;

    /**
     * Determine if a user is allowed to generate a document.
     *
     * @param  Authenticatable  $user
     * @return bool
     */
    abstract public function create(Authenticatable $user): bool;

    /**
     * Determine if a user is allowed to generate a temporary document.
     *
     * @param  Authenticatable  $user
     * @return bool
     */
    public function createTmp(Authenticatable $user): bool
    {
        return $this->create($user);
    }

    /**
     * Determine if a user is allowed to edit a document's layout.
     *
     * @param  Authenticatable  $user
     * @param  DocumentLayout  $documentLayout
     * @return bool
     */
    abstract public function editLayout(Authenticatable $user, DocumentLayout $documentLayout): bool;
}
