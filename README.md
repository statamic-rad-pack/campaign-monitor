# Manage Campaign Monitor newsletters in Statamic
[![Latest Version](https://img.shields.io/github/v/release/statamic-rad-pack/campaign-monitor)](https://github.com/statamic-rad-pack/campaign-monitor/releases)

This package provides an easy way to integrate Campaign Monitor with Statamic forms and user registrations.

## Requirements

* PHP 8.1+
* Statamic v4

## Installation

You can install this package via composer using:

```bash
composer require statamic-rad-pack/campaign-monitor
```

The package will automatically register itself.

## Configuration

Set your Campaign Monitor API Key and Client ID in your `.env` file. Instructions on how to find these [can be found here](https://help.createsend.com/admin/api-keys#:~:text=Where%20to%20find%20your%20API,API%20keys%20and%20client%20IDs.).

```yaml
CAMPAIGNMONITOR_API_KEY=your-api-key-here
CAMPAIGNMONITOR_CLIENT_ID=your-client-id-here
```

Publish the config file to `config/campaign-monitor.php` run:

```bash
php artisan vendor:publish --tag="campaign-monitor-config"
```

## Usage

Create your Statamic [forms](https://statamic.dev/forms#content) as usual. Don't forget to add the consent field to your blueprint.

*Configure Mailchimp in the Control Panel:*

![control panel](https://raw.githubusercontent.com/statamic-rad-pack/campaign-monitor/main/images/config.png)


## Testing

Run the tests with:
```bash
vendor/bin/phpunit
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

Please see [SECURITY](SECURITY.md) for details.
