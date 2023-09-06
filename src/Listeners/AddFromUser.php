<?php

namespace StatamicRadPack\CampaignMonitor\Listeners;

use Statamic\Events\UserRegistered;
use StatamicRadPack\CampaignMonitor\Subscriber;

class AddFromUser
{
    public function handle(UserRegistered $event)
    {
        if (! config('campaign-monitor.add_new_users')) {
            return;
        }

        Subscriber::fromUser($event->user)->subscribe();
    }
}
