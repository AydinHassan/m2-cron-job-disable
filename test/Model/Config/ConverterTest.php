<?php

declare(strict_types=1);

namespace TrashPanda\CronJobModifierTest\Model\Config;

use TrashPanda\CronJobModifier\Model\Config\Converter;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    public function testConvert(): void
    {
        $xml = <<<'END'
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="cron_disable.xsd">
    <group name="default">
        <remove_job>currency_rates_update</remove_job>
        <remove_job>backend_clean_cache</remove_job>
    </group>
    <group name="nosto">
        <remove_job>disable-me3</remove_job>
        <remove_job>disable-me4</remove_job>
    </group>
</config>
END;

        $domDocument = new \DOMDocument();
        $domDocument->loadXML($xml);

        self::assertEquals(
            [
                'removes' => [
                    'default' => [
                        'currency_rates_update',
                        'backend_clean_cache'
                    ],
                    'nosto' => [
                        'disable-me3',
                        'disable-me4'
                    ]
                ],
                'moves' => [
                    'default' => [],
                    'nosto' => []
                ]
            ],
            (new Converter())->convert($domDocument)
        );
    }

    public function testConvertWithMoveJob(): void
    {
        $xml = <<<'END'
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="cron_disable.xsd">
    <group name="default">
        <remove_job>currency_rates_update</remove_job>
        <remove_job>backend_clean_cache</remove_job>
        <move_job to_group="my-new-group">backend_clean_cache</move_job>
    </group>
    <group name="nosto">
        <remove_job>disable-me3</remove_job>
        <remove_job>disable-me4</remove_job>
    </group>
</config>
END;

        $domDocument = new \DOMDocument();
        $domDocument->loadXML($xml);

        self::assertEquals(
            [
                'removes' => [
                    'default' => [
                        'currency_rates_update',
                        'backend_clean_cache'
                    ],
                    'nosto' => [
                        'disable-me3',
                        'disable-me4'
                    ]
                ],
                'moves' => [
                    'default' => [
                        'backend_clean_cache' => 'my-new-group'
                    ],
                    'nosto' => []
                ]
            ],
            (new Converter())->convert($domDocument)
        );
    }
}
