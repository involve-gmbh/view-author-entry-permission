<?php

namespace Involve\ViewAuthorEntryPermission;

use Statamic\Entries\Collection;
use Statamic\Facades\User;
use Statamic\Query\Builder;

class AuthorFilterApplicator
{
    public function applyFilterIfNecessary(Collection $collection, Builder $originalQuery): Builder
    {
        if ($this->shouldSeeOtherAuthorsEntries($collection))
            return $originalQuery;
        else
            return $originalQuery->where('author', User::current()->id());
    }

    private function shouldSeeOtherAuthorsEntries(Collection $collection): bool
    {
        $handle = $collection->handle();
        return User::current()->can("view other authors $handle entries");
    }
}
