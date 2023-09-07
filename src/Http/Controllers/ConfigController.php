<?php

namespace StatamicRadPack\CampaignMonitor\Http\Controllers;

use Edalzell\Forma\ConfigController as BaseController;
use Illuminate\Support\Arr;

class ConfigController extends BaseController
{
    protected function postProcess(array $values): array
    {
        $userConfig = Arr::get($values, 'users');

        return array_merge(
            $values,
            ['users' => $userConfig[0]]
        );
    }

    protected function preProcess(string $handle): array
    {
        $config = config($handle);

        return array_merge(
            $config,
            ['users' => [Arr::get($config, 'users', [])]]
        );
    }
    
    public static function cpIcon()
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 421.08 284.91"><path d="m418.18,6.84c-5.08-7.24-15.05-9.01-22.31-3.92L2.88,278.08c2.89,4.12,7.68,6.83,13.1,6.82h.02s0,0,0,0h389.06c8.84,0,16.01-7.17,16.01-16.02V15.77c-.05-3.09-.99-6.2-2.9-8.93"/><path d="m25.21,2.9C17.96-2.18,7.98-.41,2.9,6.82.99,9.56.05,12.68,0,15.77v253.59S187.1,116.1,187.1,116.1L25.21,2.9Z"/></svg>';
    }
}
