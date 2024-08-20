<?php

namespace StatamicRadPack\CampaignMonitor;

use Edalzell\Forma\Forma;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\UserRegistered;
use Statamic\Facades\Form;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Support\Arr;
use StatamicRadPack\CampaignMonitor\Fieldtypes\CampaignMonitorCustomFields;
use StatamicRadPack\CampaignMonitor\Fieldtypes\CampaignMonitorFormFields;
use StatamicRadPack\CampaignMonitor\Fieldtypes\CampaignMonitorList;
use StatamicRadPack\CampaignMonitor\Fieldtypes\CampaignMonitorUserFields;
use StatamicRadPack\CampaignMonitor\Http\Controllers\ConfigController;
use StatamicRadPack\CampaignMonitor\Listeners\AddFromSubmission;
use StatamicRadPack\CampaignMonitor\Listeners\AddFromUser;
use Stillat\Proteus\Support\Facades\ConfigWriter;

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

        $this->addFormConfigFields();

        $this->app->booted(function () {
            $this->migrateToFormConfig();

            $this->addFormsToNewsletterConfig();
        });
    }

    private function addFormsToNewsletterConfig()
    {
        $lists = Form::all()
            ->flatMap(function ($form) {
                $data = $form->get('campaign_monitor', []);

                if (! $enabled = Arr::get($data, 'enabled')) {
                    return [];
                }

                if (! $data = Arr::get($data, 'settings', [])) {
                    return [];
                }

                return [
                    $form->handle() => Arr::removeNullValues([
                        'id' => Arr::get($data, 'list_id'),
                        'marketing_permissions' => collect(Arr::get($data, 'marketing_permissions_field_ids'))
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

    private function addFormConfigFields()
    {
        Form::appendConfigFields('*', __('Campaign Monitor Integration'), [
            'campaign_monitor' => [
                'handle' => 'campaign_monitor',
                'type' => 'group',
                'display' => ' ',
                'fullscreen' => false,
                'border' => false,
                'fields' => [
                    [
                        'handle' => 'enabled',
                        'field' => [
                            'type' => 'toggle',
                            'display' => __('Enabled'),
                            'width' => 100,
                        ],
                    ],

                    [
                        'handle' => 'settings',
                        'field' => [
                            'type' => 'group',
                            'display' => ' ',
                            'width' => 100,
                            'fullscreen' => false,
                            'show_when' => ['enabled' => true],
                            'fields' => [

                                [
                                    'handle' => 'list_id',
                                    'field' => [
                                        'type' => 'campaign_monitor_list',
                                        'max_items' => 1,
                                        'mode' => 'select',
                                        'display' => __('List ID'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'name_field',
                                    'field' => [
                                        'type' => 'campaign_monitor_form_fields',
                                        'max_items' => 1,
                                        'default' => 'name',
                                        'display' => __('Name Field'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'primary_email_field',
                                    'field' => [
                                        'type' => 'campaign_monitor_form_fields',
                                        'max_items' => 1,
                                        'default' => 'email',
                                        'display' => __('Email Field'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'check_consent',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => __('Check Consent?'),
                                        'width' => 33,
                                        'default' => false,
                                    ],
                                ],

                                [
                                    'handle' => 'consent_field',
                                    'field' => [
                                        'type' => 'campaign_monitor_form_fields',
                                        'default' => 'consent',
                                        'max_items' => 1,
                                        'display' => __('Consent Field'),
                                        'width' => 33,
                                        'if' => ['check_consent' => true],
                                    ],
                                ],

                                [
                                    'handle' => 'spacer_field',
                                    'field' => [
                                        'type' => 'spacer',
                                        'width' => 33,
                                        'if' => ['check_consent' => false],
                                    ],
                                ],

                                [
                                    'handle' => 'mobile_field',
                                    'field' => [
                                        'type' => 'campaign_monitor_form_fields',
                                        'default' => 'mobile',
                                        'max_items' => 1,
                                        'display' => __('Mobile Number Field'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'check_consent_sms',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => __('Check Consent to SMS?'),
                                        'width' => 33,
                                        'default' => false,
                                    ],
                                ],

                                [
                                    'handle' => 'consent_field_sms',
                                    'field' => [
                                        'type' => 'campaign_monitor_form_fields',
                                        'default' => 'consent_sms',
                                        'max_items' => 1,
                                        'display' => __('SMS Consent Field'),
                                        'width' => 33,
                                        'if' => ['check_consent_sms' => true],
                                    ],
                                ],

                                [
                                    'handle' => 'spacer_field_sms',
                                    'field' => [
                                        'type' => 'spacer',
                                        'width' => 33,
                                        'if' => ['check_consent_sms' => false],
                                    ],
                                ],

                                [
                                    'handle' => 'custom_fields',
                                    'field' => [
                                        'type' => 'grid',
                                        'mode' => 'table',
                                        'reorderable' => true,
                                        'listable' => 'hidden',
                                        'display' => __('Custom Fields'),
                                        'width' => 100,
                                        'add_row' => __('Add Custom Field'),
                                        'fields' => [

                                            [
                                                'handle' => 'field_name',
                                                'field' => [
                                                    'type' => 'campaign_monitor_form_fields',
                                                    'display' => __('Form Field'),
                                                    'width' => 33,
                                                ],
                                            ],

                                            [
                                                'handle' => 'key',
                                                'field' => [
                                                    'type' => 'campaign_monitor_custom_fields',
                                                    'display' => __('Custom Field'),
                                                    'max_items' => 1,
                                                    'width' => 33,
                                                ],
                                            ],

                                        ],
                                    ],
                                ],

                            ],

                        ],

                    ],

                ],
            ],
        ]);
    }

    private function migrateToFormConfig()
    {
        if (! $forms = config('campaign-monitor.forms')) {
            return;
        }

        foreach ($forms as $config) {
            (new Migrators\ConfigToFormData)->handle($config);
        }

        ConfigWriter::edit('campaign-monitor')->remove('forms')->save();
    }
}
