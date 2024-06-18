<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'dev-sent-message' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/sent-messages/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'dev-check-message-status' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/message-statuses/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'dev-user-token-update' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/user-tokens/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'dev-campaign-process' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/campaigns/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'dev-campaign-statistic' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/campaign-stats/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'dev-campaign-report' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/campaign-reports/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'dev-project-report' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/project-reports/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'dev-mailbox' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/mailboxes/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'dev-prospects' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dev/prospects/laravel.log'),
            'tap' => [App\Logging\CustomizeFormatter::class],
            'level' => 'debug',
            'days' => 14,
        ],

        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],

];
