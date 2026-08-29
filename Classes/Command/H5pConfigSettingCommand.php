<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace LMS3\Lms3h5p\Command;

use LMS3\Lms3h5p\H5PAdapter\TYPO3H5P;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * H5P Config Setting Command
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
#[AsCommand('h5p:configsetting')]
class H5pConfigSettingCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Add required H5P configuration settings to the database.');
    }

    /**
     * Add h5p settings in database table
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $typo3h5p = GeneralUtility::makeInstance(TYPO3H5P::class);
            $settings = $typo3h5p->getSettings();
            $interface = $typo3h5p->getH5PInstance();

            if (empty($settings['config'])) {
                $io->error('No H5P configuration settings found.');
                return Command::FAILURE;
            }

            foreach ($settings['config'] as $name => $value) {
                $interface->setOption($name, $value);
                $io->writeln(sprintf('  Set <info>%s</info> = %s', $name, $value));
            }

            $io->success('H5P configuration settings have been saved.');
        } catch (\Exception $e) {
            $io->error('Failed to save H5P settings: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
