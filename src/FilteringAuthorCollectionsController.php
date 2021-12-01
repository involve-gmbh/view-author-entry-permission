<?php

namespace Involve\ViewAuthorEntryPermission;

use Illuminate\Http\Request;
use Statamic\Contracts\Entries\Collection as CollectionContract;
use Statamic\CP\Column;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Facades\User;
use Statamic\Http\Controllers\CP\Collections\CollectionsController;

class FilteringAuthorCollectionsController extends CollectionsController
{
    public function __construct(
        Request $request,
        private AuthorFilterApplicator $authorFilterApplicator,
    ) {
        parent::__construct($request);
    }

    public function index()
    {
        $this->authorize('index', CollectionContract::class, __('You are not authorized to view collections.'));

        $collections = Collection::all()->filter(function ($collection) {
            return User::current()->can('view', $collection);
        })->map(function ($collection) {
            return [
                'id' => $collection->handle(),
                'title' => $collection->title(),
                'entries' => $this->getIndexQuery($collection)->count(),
                'edit_url' => $collection->editUrl(),
                'delete_url' => $collection->deleteUrl(),
                'entries_url' => cp_route('collections.show', $collection->handle()),
                'blueprints_url' => cp_route('collections.blueprints.index', $collection->handle()),
                'scaffold_url' => cp_route('collections.scaffold', $collection->handle()),
                'deleteable' => User::current()->can('delete', $collection),
                'editable' => User::current()->can('edit', $collection),
                'blueprint_editable' => User::current()->can('configure fields'),
            ];
        })->values();

        return view('statamic::collections.index', [
            'collections' => $collections,
            'columns' => [
                Column::make('title')->label(__('Title')),
                Column::make('entries')->label(__('Entries')),
            ],
        ]);
    }

    protected function getIndexQuery($collection)
    {
        $originalQuery = $collection->queryEntries()->where('site', Site::selected());

        return $this->authorFilterApplicator->applyFilterIfNecessary($collection, $originalQuery);
    }
}
