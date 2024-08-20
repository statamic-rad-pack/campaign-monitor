<?php

namespace StatamicRadPack\CampaignMonitor\Tests;

use Statamic\Facades\Blueprint as BlueprintFacade;
use Statamic\Facades\YAML;
use Statamic\Statamic;
use Statamic\Testing\AddonTestCase;
use StatamicRadPack\CampaignMonitor\ServiceProvider;

class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        Statamic::booted(function () {
            $blueprintContents = YAML::parse(file_get_contents(__DIR__.'/__fixtures__/blueprints/contact_us.yaml'));
            $blueprintFields = collect($blueprintContents['sections']['main']['fields'])
                ->keyBy(fn ($item) => $item['handle'])
                ->map(fn ($item) => $item['field'])
                ->all();

            BlueprintFacade::makeFromFields($blueprintFields)
                ->setNamespace('forms.contact_us')
                ->setHandle('contact_us')
                ->save();
        });
    }
}
