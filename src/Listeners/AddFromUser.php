<?php

namespace StatamicRadPack\CampaignMonitor\Listeners;

use Statamic\Events\UserRegistered;
use Statamic\Facades\Addon;
use StatamicRadPack\CampaignMonitor\Subscriber;

class AddFromUser
{
    public function handle(UserRegistered $event)
    {
        if (! Addon::get('statamic-rad-pack/campaign-monitor')->settings()->get('add_new_users')) {
            return;
        }

        Subscriber::fromUser($event->user)->subscribe();
    }
}
