<?php

namespace StatamicRadPack\CampaignMonitor\Fieldtypes;

use Bashy\CampaignMonitor\Facades\CampaignMonitor;
use Statamic\Fieldtypes\Relationship;

class CampaignMonitorList extends Relationship
{
    public function getIndexItems($request)
    {
        $lists = CampaignMonitor::clients(config('campaign-monitor.client_id'))->get_lists();

        if (! is_array($lists->response)) {
            return [];
        }

        return collect($lists->response ?? [])
            ->map(function ($list) {
                return ['id' => $list->ListID, 'title' => $list->Name];
            });
    }

    protected function toItemArray($id)
    {
        if (! $id) {
            return [];
        }

        if (! $list = CampaignMonitor::lists($id)->get()?->response ?? false) {
            return [];
        }

        return [
            'id' => $list->ListID,
            'title' => $list->Title,
        ];
    }
}
