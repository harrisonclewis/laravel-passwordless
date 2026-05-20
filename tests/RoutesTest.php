<?php

it('rate limits both token creation and consumption routes', function () {
    $store = app('router')->getRoutes()->getByName('passwordless.store');
    $show = app('router')->getRoutes()->getByName('passwordless.show');

    $middleware = collect($store->gatherMiddleware());

    expect($middleware->contains('throttle:6,1'))->toBeTrue()
        ->and(collect($show->gatherMiddleware())->contains('throttle:6,1'))->toBeTrue();
});
