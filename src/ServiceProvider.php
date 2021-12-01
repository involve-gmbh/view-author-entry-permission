<?php

namespace Involve\ViewAuthorEntryPermission;

use Statamic\Facades\Collection;
use Statamic\Facades\Permission;
use Statamic\Fieldtypes\Entries;
use Statamic\Http\Controllers\CP\Collections\CollectionsController;
use Statamic\Http\Controllers\CP\Collections\EntriesController;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    function register()
    {
        $this->app->bind(
            EntriesController::class,
            FilteringAuthorEntriesController::class,
        );
        $this->app->bind(
            Entries::class,
            FilteringAuthorEntries::class,
        );
        $this->app->bind(
            CollectionsController::class,
            FilteringAuthorCollectionsController::class,
        );
    }

    public function bootAddon()
    {
        $this->bootPermissions();
    }

    public function bootPermissions()
    {
        Permission::group('view-author-entry-permission', "View other author's entries", function () {
            Permission::register('view other authors {collection} entries', function ($permission) {
                $permission
                    ->label("View other author's :collection entries")
                    ->replacements('collection', function () {
                        return Collection::all()->map(function ($collection) {
                            return ['value' => $collection->handle(), 'label' => $collection->title()];
                        });
                    });
            });
        });
    }
}
