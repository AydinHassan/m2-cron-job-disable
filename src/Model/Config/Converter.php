<?php

declare(strict_types=1);

namespace TrashPanda\CronJobModifier\Model\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{

    public function convert($source)
    {
        $groups = $source->getElementsByTagName('group');

        $removes = [];
        $moves = [];
        foreach ($groups as $group) {
            $groupName = $group->getAttribute('name');

            if (!isset($removes[$groupName])) {
                $removes[$groupName] = [];
            }

            if (!isset($moves[$groupName])) {
                $moves[$groupName] = [];
            }

            foreach ($group->getElementsByTagName('remove_job') as $jobToRemove) {
                $removes[$groupName][] = $jobToRemove->textContent;
            }

            foreach ($group->getElementsByTagName('move_job') as $jobToMove) {
                $moves[$groupName][$jobToMove->textContent] = $jobToMove->getAttribute('to_group');
            }
        }

        return ['removes' => $removes, 'moves' => $moves];
    }
}
