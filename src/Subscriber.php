<?php

namespace StatamicRadPack\CampaignMonitor;

use Bashy\CampaignMonitor\Facades\CampaignMonitor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Statamic\Auth\User;
use Statamic\Forms\Submission;
use Statamic\Support\Arr;

class Subscriber
{
    private Collection $data;

    private Collection $config;

    public static function fromSubmission(Submission $submission): self
    {
        return new self(
            $submission->data(),
            Arr::first(
                config('campaign-monitor.forms', []),
                fn (array $formConfig) => $formConfig['form'] == $submission->form()->handle()
            )
        );
    }

    public static function fromUser(User $user): self
    {
        return new self(
            $user->data()->merge(['email' => $user->email()])->all(),
            array_merge(config('campaign-monitor.users', []), ['form' => 'user'])
        );
    }

    /**
     * @param  array|Collection  $data
     */
    public function __construct($data, array $config = null)
    {
        $this->data = collect($data);
        $this->config = collect($config);
    }

    private function email(): string
    {
        return $this->get($this->config->get('primary_email_field', 'email')) ?? '';
    }
    
    private function mobile(): string
    {
        return $this->get($this->config->get('mobile_field', 'mobile')) ?? '';
    }
    
    private function name(): string
    {
        return $this->get($this->config->get('name_field', 'name')) ?? '';
    }
    
    public function hasConsent(): bool
    {
        if (! $this->config->get('check_consent', false)) {
            return true;
        }

        if (! $field = $this->config->get('consent_field', 'consent')) {
            return false;
        }

        return filter_var(
            Arr::get(Arr::wrap($this->get($field, false)), 0, false),
            FILTER_VALIDATE_BOOLEAN
        );
    }
    
    public function hasSmsConsent(): bool
    {
        if (! $this->config->get('check_consent_sms', false)) {
            return false;
        }

        if (! $field = $this->config->get('consent_field_sms', 'consent_sms')) {
            return false;
        }

        return filter_var(
            Arr::get(Arr::wrap($this->get($field, false)), 0, false),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    private function get(?string $field, $default = null)
    {
        return $this->data->get($field, $default);
    }

    public function subscribe(): void
    {
        if ($this->config->isEmpty()) {
            return;
        }

        if (! ($this->hasConsent() || $this->hasSmsConsent())) {
            return;
        }
        
        $payload = [
            'EmailAddress' => $this->email(),
            'MobileNumber' => $this->mobile(),
            'Name' => $this->name(),
            'ConsentToTrack' => $this->hasConsent() ? 'Yes' : 'No',
            'ConsentToSendSms' => $this->hasSmsConsent() ? 'Yes' : 'No',
            'Resubscribe' => true,
            'CustomFields' => collect($this->config->get('custom_fields', []))
                ->map(function ($item, $key) {
                    if (is_null($fieldData = $this->get($item['field_name']))) {
                        return [];
                    }
        
                    return [
                        'Key' => $item['key'],
                        'Value' => $fieldData,
                    ];
                })
                    ->filter()
                    ->all(),
        ];
                        
        $result = CampaignMonitor::subscribers($this->config->get('list_id'))->add($payload);
                
        if (! in_array($result->http_status_code, [200, 201])) {
            Log::error(json_encode($payload));
            Log::error(json_encode($result));
        }
    }
}
