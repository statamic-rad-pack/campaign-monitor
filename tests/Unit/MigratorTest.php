<?php

namespace StatamicRadPack\CampaignMonitor\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Form;
use StatamicRadPack\CampaignMonitor\Migrators\ConfigToFormData;
use StatamicRadPack\CampaignMonitor\Tests\TestCase;

class MigratorTest extends TestCase
{
    #[Test]
    public function it_migrates_config_data_to_the_form()
    {
        Form::make('test')
            ->handle('test')
            ->data([])
            ->save();

        (new ConfigToFormData)
            ->handle([
                'form' => 'test',
                'check_consent' => true,
            ]);

        $data = Form::find('test')->data()->all();

        $this->assertArrayHasKey('campaign_monitor', $data);

        $this->assertSame($data['campaign_monitor'], [
            'enabled' => true,
            'settings' => [
                'check_consent' => true,
            ],
        ]);
    }
}
