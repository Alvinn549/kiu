<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('service.{serviceId}', function ($user, $serviceId) {
    return $user->canAccessService($serviceId);
});

// public test
Broadcast::channel('public-channel', function () {
    return true;
});
