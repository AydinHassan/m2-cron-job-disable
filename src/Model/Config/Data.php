<?php

declare(strict_types=1);

namespace TrashPanda\CronJobModifier\Model\Config;

class Data extends \Magento\Framework\Config\Data
{
    public function getJobsToRemoveByGroup(): array
    {
        return $this->get('removes');
    }

    public function getJobsToMoveByGroup(): array
    {
        return $this->get('moves');
    }

    public function getJobsToRemoveForGroup(string $group): array
    {
        $all = $this->getJobsToRemoveByGroup();

        if (isset($all[$group])) {
            return $all[$group];
        }

        return [];
    }

    public function getJobsToMoveForGroup(string $group): array
    {
        $all = $this->getJobsToMoveByGroup();

        if (isset($all[$group])) {
            return $all[$group];
        }

        return [];
    }
}
