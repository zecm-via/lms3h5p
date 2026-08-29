H5P Platform Integration Extension for TYPO3 CMS
============

What is H5P?
============

H5P is a plugin for existing publishing systems that enables the system to create interactive content like Interactive Videos, Presentations, Games, Quizzes and more!
For more information visit https://h5p.org/

What does this extension do?
============

This extension provides a platform integration for H5P into the TYPO3 CMS (https://typo3.org/), allowing
you to use H5P content types within TYPO3 CMS.

This extension works independendly inside a TYPO3 Installation but is part of the TYPO3 based E-Learning System LMS3 http://www.lms3.de created by LEARNTUBE GmbH.

Installation
============

Installation is simple and consists of these steps:

1. Run `composer require "lms3/lms3h5p:v13.x-dev"`
2. Make sure `typo3/cms-fluid-styled-content` installed.

Configuration
============

1. Make sure database tables are created.
2. Include the static TypoScript of the extension
3. Unzip `EXT:Resources/Private/Files/h5p.zip` into your **fileadmin** directory
4. Run `./vendor/bin/typo3 lms3h5p:h5p:configsetting`
5. Run  `./vendor/bin/typo3 lms3h5p:h5p:copyresources`

H5P Content Preparation
============

1. Create new folder in page tree
2. Click on LMS3: H5P Content and select storage folder
3. Click on `Create new content` icon to create new H5P Content
4. Install H5P Library and use it.

Render Content
============

1. Go to page and add content
2. Select LMS3: H5P Content
3. Select H5P Content from the storage folder
4. Save and Close
5. View content in frontend

Change styling
============

1. Create css file e.g. EXT:theme/Resources/Public/css/lms3h5p.css
2. Configure typoscript as follows:

```
module.tx_lms3h5p.settings {
    customStyle {
        path = EXT:theme/Resources/Public/css/lms3h5p.css
        version = 20190601150000
    }
}
```

Note: After your css changes, don't forget to change the version number.
