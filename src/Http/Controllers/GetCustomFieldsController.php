<?php

namespace StatamicRadPack\CampaignMonitor\Http\Controllers;

use Bashy\CampaignMonitor\Facades\CampaignMonitor;
use Illuminate\Support\Arr;
use Statamic\Http\Controllers\Controller;

class GetCustomFieldsController extends Controller
{
    public function __invoke(string $list): array
    {
        $fields = CampaignMonitor::lists($list)->get_custom_fields();

        return collect($fields->response ?? [])
            ->map(fn ($mergeField) => ['id' => $mergeField->Key, 'label' => $mergeField->FieldName])
            ->values()
            ->all();
    }
}
