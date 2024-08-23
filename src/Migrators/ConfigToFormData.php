<?php

namespace StatamicRadPack\CampaignMonitor\Migrators;

use Statamic\Facades\Form;
use Statamic\Support\Arr;

class ConfigToFormData
{
    public function handle(array $config)
    {
        if (! $handle = $config['form'] ?? '') {
            return;
        }

        if (! $form = Form::find($handle)) {
            return;
        }

        $form->merge([
            'campaign_monitor' => [
                'enabled' => true,
                'settings' => Arr::except($config, ['form', 'id']),
            ],
        ])->saveQuietly();
    }
}
