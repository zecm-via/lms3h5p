<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Command;

use LMS3\Lms3h5p\Domain\Model\Library;
use LMS3\Lms3h5p\Domain\Repository\LibraryDependencyRepository;
use LMS3\Lms3h5p\Domain\Repository\LibraryRepository;
use LMS3\Lms3h5p\H5PAdapter\Core\H5PFramework;
use LMS3\Lms3h5p\H5PAdapter\TYPO3H5P;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * H5P Repair Library Command
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
#[AsCommand('h5p:repairLibrary')]
class H5pRepairLibraryCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Repair H5P library dependencies by re-reading them from library.json.');
        $this->addArgument(
            'machineName',
            InputArgument::REQUIRED,
            'The library name with version e.g. H5P.MultiChoice-1.16'
        );
    }

    /**
     * Repair library dependencies from filesystem
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var LibraryRepository $libraryRepository */
        $libraryRepository = GeneralUtility::makeInstance(LibraryRepository::class);
        $libraryRepository->setDefaultQuerySettings(
            $libraryRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
        /** @var LibraryDependencyRepository $libraryDependencyRepository */
        $libraryDependencyRepository = GeneralUtility::makeInstance(LibraryDependencyRepository::class);
        $libraryDependencyRepository->setDefaultQuerySettings(
            $libraryDependencyRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );

        try {
            $libraryJsonPath = sprintf(
                '%s/fileadmin/h5p/libraries/%s/library.json',
                Environment::getPublicPath(),
                $input->getArgument('machineName')
            );

            /** @var H5PFramework $interface */
            $interface = GeneralUtility::makeInstance(TYPO3H5P::class)->getH5PInstance();

            if (!file_exists($libraryJsonPath)) {
                $io->error('Library not found on filesystem: ' . $libraryJsonPath);
                return Command::FAILURE;
            }

            $json = file_get_contents($libraryJsonPath);
            if (!json_validate($json)) {
                $io->error('Invalid JSON in library.json');
                return Command::FAILURE;
            }

            $libraryArray = json_decode($json, true);
            $io->writeln('-> Finding ' . $libraryArray['machineName'] . ' in the database');

            $library = $libraryRepository->findOneByNameMajorVersionAndMinorVersion(
                $libraryArray['machineName'],
                (int)$libraryArray['majorVersion'],
                (int)$libraryArray['minorVersion'],
            );

            if (!$library instanceof Library) {
                $io->error($libraryArray['machineName'] . ' not found in the database');
                return Command::FAILURE;

            }
            $io->writeln('<info>' . $libraryArray['machineName'] . ' found in the database</info>');
            $io->writeln('-> Checking library dependencies');

            $dependencies = $library->getLibraryDependencies();
            if ($dependencies->count() === 0) {
                $io->writeln($libraryArray['machineName'] . ' library dependencies not found in the database');
            } else {
                $io->writeln($libraryArray['machineName'] . ' library dependencies found in the database');
            }

            $totalDependencyCount = count($libraryArray['preloadedDependencies'] ?? [])
                + count($libraryArray['editorDependencies'] ?? [])
                + count($libraryArray['dynamicDependencies'] ?? []);

            if ($dependencies->count() !== $totalDependencyCount) {
                $io->writeln($libraryArray['machineName'] . ' library dependencies count doesn\'t match, actual count is ' . $totalDependencyCount);

                if (isset($libraryArray['preloadedDependencies'])) {
                    $interface->saveLibraryDependencies($library->getUid(), $libraryArray['preloadedDependencies'], 'preloaded');
                }
                if (isset($libraryArray['dynamicDependencies'])) {
                    $interface->saveLibraryDependencies($library->getUid(), $libraryArray['dynamicDependencies'], 'dynamic');
                }
                if (isset($libraryArray['editorDependencies'])) {
                    $interface->saveLibraryDependencies($library->getUid(), $libraryArray['editorDependencies'], 'editor');
                }

                $io->success('Library dependencies have been repaired.');
            } else {
                $io->success($libraryArray['machineName'] . ' library dependencies count (' . $totalDependencyCount . ') matched - no repair needed.');
            }
        } catch (\Exception $e) {
            $io->error('Failed to repair library: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
