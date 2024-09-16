<?php

namespace StatamicRadPack\CampaignMonitor\Listeners;

use Statamic\Events\SubmissionCreated;
use StatamicRadPack\CampaignMonitor\Subscriber;

class AddFromSubmission
{
    public function handle(SubmissionCreated $event)
    {
        Subscriber::fromSubmission($event->submission)?->subscribe();
    }
}
