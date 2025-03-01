<?php

/**
 * @file api/v1/stats/publications/StatsPublicationHandler.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class StatsPublicationHandler
 * @ingroup api_v1_stats
 *
 * @brief Handle API requests for publication statistics.
 *
 */

namespace APP\API\v1\stats\publications;
 
class StatsPublicationHandler extends \PKP\API\v1\stats\publications\PKPStatsPublicationHandler
{
    /** @var string The name of the section ids query param for this application */
    public $sectionIdsQueryParam = 'sectionIds';
}
