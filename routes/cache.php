<?php
use Diagro\Backend\Jobs\DeleteResourceCache;
use Diagro\Backend\Middleware\WithBackendAppId;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware(WithBackendAppId::class)->prefix('/diagro/cache')->group(function() {

    //delete cache entry
    Route::delete('/', function(Request $request) {
        $resources = $request->validate([
            'resources' => 'required|array'
        ])['resources'];

        foreach($resources as $resource) {
            DeleteResourceCache::dispatchAfterResponse($resource);
        }
    });

});