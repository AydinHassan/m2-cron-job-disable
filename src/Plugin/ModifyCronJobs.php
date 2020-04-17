<?php

declare(strict_types=1);

namespace TrashPanda\CronJobModifier\Plugin;

use Magento\Cron\Model\Config;
use TrashPanda\CronJobModifier\Model\Config\Data;

class ModifyCronJobs
{
    /**
     * @var Data
     */
    private $config;

    public function __construct(Data $config)
    {
        $this->config = $config;
    }

    public function afterGetJobs(Config $subject, array $jobs): array
    {
        $jobs = $this->removeDisabledJobs($jobs);
        $jobs = $this->moveJobGroups($jobs);

        return $jobs;
    }

    public function removeDisabledJobs(array $jobs): array
    {
        return array_combine(
            array_keys($jobs),
            array_map(function (array $jobs, string $group) {
                return array_diff_key(
                    $jobs,
                    array_flip($this->config->getJobsToRemoveForGroup($group))
                );
            }, $jobs, array_keys($jobs))
        );
    }

    private function moveJobGroups(array $jobs): array
    {
        foreach ($jobs as $group => $groupJobs) {
            $moves = $this->config->getJobsToMoveForGroup($group);

            foreach ($moves as $job => $destinationGroup) {
                if (isset($jobs[$destinationGroup]) && isset($groupJobs[$job])) {
                    $jobs[$destinationGroup][$job] = $groupJobs[$job];
                    unset($jobs[$group][$job]);
                }
            }
        }


        return $jobs;
    }
}
