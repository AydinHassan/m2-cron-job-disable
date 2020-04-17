<h1 align="center">Magento 2 Cron Job Modifier</h1>

<p align="center">
    <a href="https://travis-ci.org/AydinHassan/m2-cron-job-modify" title="Build Status" target="_blank">
        <img src="https://img.shields.io/travis/AydinHassan/m2-cron-job-modify/master.svg?style=flat-square&label=Linux" />
    </a>
</p>

<p align="center">Remove cron jobs or move them to other groups</p>

## Installation

```sh
$ composer require trash-panda/m2-cron-job-modify
$ php bin/magento setup:upgrade
```

## Usage

You will need to create a module and add a `cron_modify.xml` file in to the `etc` directory.

### Remove a cron job

To remove a cron job, create a group node and use the name attribute to specify the group. Then specify the jobs to
remove using their job codes inside the `remove_job` node.

```xml
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:TrashPanda_CronJobModify:etc/cron_modify.xsd">
    <group name="default">
        <remove_job>job_code_1</remove_job>
        <remove_job>job_code_2</remove_job>
    </group>
</config>
```

### Move a cron job in to a different group

This is useful to isolate slow jobs and run them in parallel, or for isolating jobs which can crash, keeping the rest of 
the jobs running correctly.

To move a cron job, create a group node and use the name attribute to specify the group where the job originally lives. 
Then specify the jobs to move using their job codes inside the `move_job` node. Specify the destination group in the
`to_group` attribute. 

Note: The destination group must already exist (create it using the default method)

```xml
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:TrashPanda_CronJobModify:etc/cron_modify.xsd">
    <group name="default">
        <move_job to_group="my-isolated-group">backend_clean_cache</move_job>
    </group>
</config>
```
