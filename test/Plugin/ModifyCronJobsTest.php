<?php

declare(strict_types=1);

namespace TrashPanda\CronJobModifierTest\Plugin;

use TrashPanda\CronJobModifier\Model\Config\Data;
use TrashPanda\CronJobModifier\Plugin\ModifyCronJobs;
use PHPUnit\Framework\TestCase;

class DisableCronJobsTest extends TestCase
{
    private $jobs = [
        'default' => [
            'currency_rates_update' => [
                'name' => 'currency_rates_update',
                'instance' => 'Magento\\Directory\\Model\\Observer',
                'method' => 'scheduledUpdateCurrencyRates',
                'config_path' => 'crontab/default/jobs/currency_rates_update/schedule/cron_expr',
            ],
            'backend_clean_cache' => [
                'name' => 'backend_clean_cache',
                'instance' => 'Magento\\Backend\\Cron\\CleanCache',
                'method' => 'execute',
                'schedule' => '30 2 * * *',
            ],
            'visitor_clean' => [
                'name' => 'visitor_clean',
                'instance' => 'Magento\\Customer\\Model\\Visitor',
                'method' => 'clean',
                'schedule' => '0 0 * * *',
            ],
            'system_backup' => [
                'name' => 'system_backup',
                'instance' => 'Magento\\Backup\\Cron\\SystemBackup',
                'method' => 'execute',
            ],
            'catalog_index_refresh_price' => [
                'name' => 'catalog_index_refresh_price',
                'instance' => 'Magento\\Catalog\\Cron\\RefreshSpecialPrices',
                'method' => 'execute',
                'schedule' => '0 * * * *',
            ],
        ],
        'scconnector' => [
            'scconnector_verify_website' => [
                'name' => 'scconnector_verify_website',
                'instance' => 'Magento\\GoogleShoppingAds\\Cron\\SiteVerification',
                'method' => 'execute',
                'schedule' => '*/10 * * * *',
            ],
            'scconnector_retrieve_gtag' => [
                'name' => 'scconnector_retrieve_gtag',
                'instance' => 'Magento\\GoogleShoppingAds\\Cron\\GTagRetriever',
                'method' => 'execute',
                'schedule' => '*/10 * * * *',
            ],
        ],
    ];

    public function testJobsAreRemoved(): void
    {
        $conf = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $conf
            ->method('get')
            ->willReturnCallback(function (string $action) {
                return [
                    'removes' => [
                        'default' => [
                            'currency_rates_update',
                            'visitor_clean'
                        ],
                        'scconnector' => [
                            'scconnector_verify_website',
                            'scconnector_retrieve_gtag'
                        ]
                    ],
                    'moves' => []
                ][$action];
            });

        $cronConfig = $this->createMock(\Magento\Cron\Model\Config::class);

        $plugin = new ModifyCronJobs($conf);
        $jobs = $plugin->afterGetJobs($cronConfig, $this->jobs);

        $expected = [
            'default' => [
                'backend_clean_cache' => [
                    'name' => 'backend_clean_cache',
                    'instance' => 'Magento\\Backend\\Cron\\CleanCache',
                    'method' => 'execute',
                    'schedule' => '30 2 * * *',
                ],
                'system_backup' => [
                    'name' => 'system_backup',
                    'instance' => 'Magento\\Backup\\Cron\\SystemBackup',
                    'method' => 'execute',
                ],
                'catalog_index_refresh_price' => [
                    'name' => 'catalog_index_refresh_price',
                    'instance' => 'Magento\\Catalog\\Cron\\RefreshSpecialPrices',
                    'method' => 'execute',
                    'schedule' => '0 * * * *',
                ],
            ],
            'scconnector' => []
        ];

        self::assertEquals($expected, $jobs);
    }

    public function testJobsAreMoved(): void
    {
        $conf = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $conf
            ->method('get')
            ->willReturnCallback(function (string $action) {
                return [
                    'removes' => [],
                    'moves' => [
                        'default' => [
                            'currency_rates_update' => 'scconnector',
                            'visitor_clean' => 'scconnector'
                        ]
                    ]
                ][$action];
            });

        $cronConfig = $this->createMock(\Magento\Cron\Model\Config::class);

        $plugin = new ModifyCronJobs($conf);
        $jobs = $plugin->afterGetJobs($cronConfig, $this->jobs);

        $expected = [
            'default' => [
                'backend_clean_cache' => [
                    'name' => 'backend_clean_cache',
                    'instance' => 'Magento\\Backend\\Cron\\CleanCache',
                    'method' => 'execute',
                    'schedule' => '30 2 * * *',
                ],
                'system_backup' => [
                    'name' => 'system_backup',
                    'instance' => 'Magento\\Backup\\Cron\\SystemBackup',
                    'method' => 'execute',
                ],
                'catalog_index_refresh_price' => [
                    'name' => 'catalog_index_refresh_price',
                    'instance' => 'Magento\\Catalog\\Cron\\RefreshSpecialPrices',
                    'method' => 'execute',
                    'schedule' => '0 * * * *',
                ],
            ],
            'scconnector' => [
                'scconnector_verify_website' => [
                    'name' => 'scconnector_verify_website',
                    'instance' => 'Magento\\GoogleShoppingAds\\Cron\\SiteVerification',
                    'method' => 'execute',
                    'schedule' => '*/10 * * * *',
                ],
                'scconnector_retrieve_gtag' => [
                    'name' => 'scconnector_retrieve_gtag',
                    'instance' => 'Magento\\GoogleShoppingAds\\Cron\\GTagRetriever',
                    'method' => 'execute',
                    'schedule' => '*/10 * * * *',
                ],
                'currency_rates_update' => [
                    'name' => 'currency_rates_update',
                    'instance' => 'Magento\\Directory\\Model\\Observer',
                    'method' => 'scheduledUpdateCurrencyRates',
                    'config_path' => 'crontab/default/jobs/currency_rates_update/schedule/cron_expr',
                ],
                'visitor_clean' => [
                    'name' => 'visitor_clean',
                    'instance' => 'Magento\\Customer\\Model\\Visitor',
                    'method' => 'clean',
                    'schedule' => '0 0 * * *',
                ],
            ]
        ];

        self::assertEquals($expected, $jobs);
    }

    public function testNonExistingJobsAreSkippedWhenMoving(): void
    {
        $conf = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $conf
            ->method('get')
            ->willReturnCallback(function (string $action) {
                return [
                    'removes' => [],
                    'moves' => [
                        'default' => [
                            'non-existing-job' => 'scconnector',
                        ]
                    ]
                ][$action];
            });

        $cronConfig = $this->createMock(\Magento\Cron\Model\Config::class);

        $plugin = new ModifyCronJobs($conf);
        $jobs = $plugin->afterGetJobs($cronConfig, ['default' => [], 'scconnector' => []]);

        $expected = [
            'default' => [],
            'scconnector' => []
        ];

        self::assertEquals($expected, $jobs);
    }

    public function testJobsAreNotMovedIfDestinationGroupDoesNotExist(): void
    {
        $conf = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $conf
            ->method('get')
            ->willReturnCallback(function (string $action) {
                return [
                    'removes' => [],
                    'moves' => [
                        'default' => [
                            'my-job' => 'non-existing-group',
                        ]
                    ]
                ][$action];
            });

        $cronConfig = $this->createMock(\Magento\Cron\Model\Config::class);

        $plugin = new ModifyCronJobs($conf);
        $jobs = $plugin->afterGetJobs($cronConfig, ['default' => ['my-job' => []], 'scconnector' => []]);

        $expected = [
            'default' => ['my-job' => []],
            'scconnector' => []
        ];

        self::assertEquals($expected, $jobs);
    }
}
