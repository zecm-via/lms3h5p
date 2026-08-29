<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Command;

use LMS3\Lms3h5p\Setup;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * H5P Copy Resources Command
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
#[AsCommand('h5p:copyresources')]
class H5pCopyResourcesCommand extends Command
{
    public function __construct(private readonly Setup $setup)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Copy required H5P core and editor resources from vendor packages to fileadmin.');
    }

    /**
     * Copy required resources from h5p vendor packages
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->info('Copying H5P core and editor resources...');
            $this->setup->copyResourcesFromH5PLibraries();
            $io->success('H5P resources have been copied successfully.');
        } catch (\Exception $e) {
            $io->error('Failed to copy H5P resources: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
