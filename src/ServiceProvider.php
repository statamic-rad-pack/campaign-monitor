<?php

namespace StatamicRadPack\CampaignMonitor;

use Edalzell\Forma\Forma;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\UserRegistered;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Support\Arr;
use StatamicRadPack\CampaignMonitor\Fieldtypes\CampaignMonitorCustomFields;
use StatamicRadPack\CampaignMonitor\Fieldtypes\CampaignMonitorFormFields;
use StatamicRadPack\CampaignMonitor\Fieldtypes\CampaignMonitorList;
use StatamicRadPack\CampaignMonitor\Fieldtypes\CampaignMonitorUserFields;
use StatamicRadPack\CampaignMonitor\Http\Controllers\ConfigController;
use StatamicRadPack\CampaignMonitor\Listeners\AddFromSubmission;
use StatamicRadPack\CampaignMonitor\Listeners\AddFromUser;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        CampaignMonitorCustomFields::class,
        CampaignMonitorList::class,
        CampaignMonitorFormFields::class,
        CampaignMonitorUserFields::class,
    ];

    protected $listen = [
        UserRegistered::class => [AddFromUser::class],
        SubmissionCreated::class => [AddFromSubmission::class],
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    protected $vite = [
        'input' => ['resources/js/cp.js'],
        'publicDirectory' => 'dist',
        'hotFile' => __DIR__.'/../dist/hot',
    ];

    public function boot()
    {
        parent::boot();

        Forma::add('statamic-rad-pack/campaign-monitor', ConfigController::class);

        $this->app->booted(function () {
            $this->addFormsToNewsletterConfig();
        });
    }

    private function addFormsToNewsletterConfig()
    {
        $lists = collect(config('campaign-monitor.forms'))
            ->flatMap(function ($form) {
                if (! $handle = Arr::get($form, 'form')) {
                    return [];
                }

                return [
                    $handle => Arr::removeNullValues([
                        'id' => Arr::get($form, 'list_id'),
                        'marketing_permissions' => collect(Arr::get($form, 'marketing_permissions_field_ids'))
                            ->filter()
                            ->flatMap(fn ($value) => [$value['field_name'] => $value['id']])
                            ->all(),
                    ]),
                ];
            })
            ->all();

        $lists['user'] = ['id' => config('campaign-monitor.users.list_id')];

        config(['campaign-monitor.lists' => $lists]);
    }
}
