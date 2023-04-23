<?php

declare(strict_types=1);

namespace Tests\Extensions;

use Codeception\Events;
use Codeception\Extension;
use Codeception\Module\Cli;

class DatabaseMigrationExtension extends Extension
{
    public static array $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite()
    {
        try {
            /** @var Cli $cli */
            $cli = $this->getModule('Cli');

            $this->writeln('Running Doctrine Migrations...');
            $cli->runShellCommand('bin/console d:m:m --env=test --no-interaction');
            $cli->seeResultCodeIs(0);

            $this->writeln('Test database recreated');
        } catch (\Exception $e) {
            $this->writeln(
                sprintf(
                    'An error occurred whilst rebuilding the test database: %s',
                    $e->getMessage()
                )
            );
        }
    }
}
