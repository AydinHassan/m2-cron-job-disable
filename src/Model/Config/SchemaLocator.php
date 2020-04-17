<?php

declare(strict_types=1);

namespace TrashPanda\CronJobModifier\Model\Config;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

class SchemaLocator implements SchemaLocatorInterface
{
    private const CONFIG_FILE_SCHEMA = 'cron_disable.xsd';

    private $schema;

    public function __construct(Reader $moduleReader)
    {
        $this->schema = sprintf(
            '%s/%s',
            $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'TrashPanda_CronJobDisable'),
            self::CONFIG_FILE_SCHEMA
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerFileSchema()
    {
        return $this->schema;
    }
}
