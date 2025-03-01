<?php

/**
 * @file classes/submission/form/SubmissionSubmitStep2Form.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SubmissionSubmitStep2Form
 * @ingroup submission_form
 *
 * @brief Form for Step 2 of author manuscript submission.
 */

namespace APP\submission\form;

use APP\template\TemplateManager;

use PKP\submission\form\PKPSubmissionSubmitStep2Form;

class SubmissionSubmitStep2Form extends PKPSubmissionSubmitStep2Form
{
    /**
     * @copydoc SubmissionSubmitForm::fetch
     *
     * @param null|mixed $template
     */
    public function fetch($request, $template = null, $display = false)
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('requestArgs', [
            'submissionId' => $this->submission->getId(),
            'publicationId' => $this->submission->getCurrentPublication()->getId(),
        ]);
        return parent::fetch($request, $template, $display);
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\submission\form\SubmissionSubmitStep2Form', '\SubmissionSubmitStep2Form');
}
