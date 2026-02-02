<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('service.{serviceId}', function ($user, $serviceId) {
    return $user->canAccessService($serviceId);
});

Broadcast::channel('touch', function () {
    return true;
});

Broadcast::channel('main-display', function () {
    return true;
});

Broadcast::channel('new-ticket', function () {
    return true;
});
