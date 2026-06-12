<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;

class MarkUserAsVerified
{
    public function handle(Verified $event): void
    {
        $user = $event->user;
        if ($user->status === 'pending') {
            $user->status = 'verified';
            $user->save();
        }
    }
}
