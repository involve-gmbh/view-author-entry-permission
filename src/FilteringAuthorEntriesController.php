<?php

namespace Involve\ViewAuthorEntryPermission;

use Illuminate\Http\Request;
use Statamic\Facades\User;
use Statamic\Http\Controllers\CP\Collections\EntriesController;

class FilteringAuthorEntriesController extends EntriesController
{
    public function __construct(
        Request $request,
        private AuthorFilterApplicator $authorFilterApplicator,
    )
    {
        parent::__construct($request);
    }

    protected function indexQuery($collection)
    {
        $query = parent::indexQuery($collection);
        return $this->authorFilterApplicator->applyFilterIfNecessary($collection, $query);
    }
}
