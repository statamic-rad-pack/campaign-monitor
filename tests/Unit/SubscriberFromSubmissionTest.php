<?php

namespace StatamicRadPack\CampaignMonitor\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Form as FormAPI;
use Statamic\Forms\Form;
use Statamic\Forms\Submission;
use StatamicRadPack\CampaignMonitor\Subscriber;
use StatamicRadPack\CampaignMonitor\Tests\TestCase;

class SubscriberFromSubmissionTest extends TestCase
{
    private Form $form;

    private Submission $submission;

    public function setUp(): void
    {
        parent::setUp();

        $this->form = FormAPI::make('contact_us')
            ->title('Contact Us')
            ->honeypot('winnie');

        $this->form->save();

        $this->submission = $this->form->makeSubmission();
        $this->submission
            ->data([
                'email' => 'foo@bar.com',
                'first_name' => 'Foo',
                'last_name' => 'Bar',
            ]);
    }

    #[Test]
    public function can_create_subscriber_from_submission()
    {
        $formConfig = [[
            'form' => 'contact_us',
            'check_consent' => true,
        ]];

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $this->assertInstanceOf(Subscriber::class, $subscriber);
    }

    #[Test]
    public function has_consent_by_default()
    {
        $formConfig = [
            'form' => 'post',
        ];

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertTrue($consent);
    }

    #[Test]
    public function no_consent_when_no_consent_field()
    {
        $formConfig = [
            'form' => 'post',
            'check_consent' => true,
        ];

        config(['campaign-monitor.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertFalse($consent);
    }

    #[Test]
    public function no_consent_when_consent_field_is_false()
    {
        $formConfig = [
            'form' => 'post',
            'check_consent' => true,
        ];

        $this->submission->set('consent_field', false);

        config(['campaign-monitor.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertFalse($consent);
    }

    #[Test]
    public function consent_when_default_consent_field_is_true()
    {
        $formConfig = [
            'form' => 'post',
            'check_consent' => true,
        ];

        $this->submission->set('consent', true);

        config(['campaign-monitor.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertTrue($consent);
    }

    #[Test]
    public function consent_when_configured_consent_field_is_true()
    {
        $formConfig =
            [
                'blueprint' => 'post',
                'check_consent' => true,
                'consent_field' => 'the-consent',
            ];

        $this->submission->set('the-consent', true);

        config(['campaign-monitor.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertTrue($consent);
    }
    
    #[Test]
    public function it_gets_config_from_form()
    {
        $settings = [
            'check_consent' => true,
            'primary_email_field' => 'email',
        ];

        $this->form->merge([
            'campaign_monitor' => [
                'enabled' => true,
                'settings' => $settings,
            ],
        ])->save();

        $subscriber = Subscriber::fromSubmission($this->submission);

        $this->assertSame($settings, $subscriber->config());
        $this->assertSame($subscriber->email(), 'foo@bar.com');
    }
}
