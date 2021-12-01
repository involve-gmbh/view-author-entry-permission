<?php

namespace Involve\ViewAuthorEntryPermission;

use Statamic\Facades\Collection;
use Statamic\Fieldtypes\Entries;

class FilteringAuthorEntries extends Entries
{
    public function __construct(
        private AuthorFilterApplicator $authorFilterApplicator
    ) {
    }

    protected function getIndexQuery($request)
    {
        $query = parent::getIndexQuery($request);

        $collectionHandles = $this->getConfiguredCollections();
        if (is_null($collectionHandles) && count($collectionHandles) != 1)
            return $query;

        $collection = Collection::findByHandle($collectionHandles[0]);

        return $this->authorFilterApplicator->applyFilterIfNecessary($collection, $query);
    }
}
