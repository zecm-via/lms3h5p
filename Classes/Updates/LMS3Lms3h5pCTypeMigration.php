<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard('lms3Lms3h5pCTypeMigration')]
final class LMS3Lms3h5pCTypeMigration extends AbstractListTypeToCTypeUpdate
{
    public function getTitle(): string
    {
        return 'Migrate "LMS3 Lms3h5p" plugins to content elements.';
    }

    public function getDescription(): string
    {
        return 'The "LMS3 Lms3h5p" plugins are now registered as content element. Update migrates existing records and backend user permissions.';
    }

    /**
     * This must return an array containing the "list_type" to "CType" mapping
     *
     *  Example:
     *
     *  [
     *      'pi_plugin1' => 'pi_plugin1',
     *      'pi_plugin2' => 'new_content_element',
     *  ]
     *
     * @return array<string, string>
     */
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'lms3h5p_pi1' => 'lms3h5p_pi1',
        ];
    }
}
