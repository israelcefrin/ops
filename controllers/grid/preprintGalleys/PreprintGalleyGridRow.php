<?php

/**
 * @file controllers/grid/preprintGalleys/PreprintGalleyGridRow.php
 *
 * Copyright (c) 2016-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PreprintGalleyGridRow
 * @ingroup controllers_grid_preprintGalleys
 *
 * @brief Representation of an preprint galley grid row.
 */

namespace APP\controllers\grid\preprintGalleys;

use PKP\controllers\grid\GridRow;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\linkAction\request\RemoteActionConfirmationModal;
use PKP\security\Role;
use PKP\submissionFile\SubmissionFile;
use PKP\controllers\api\file\linkAction\AddFileLinkAction;

class PreprintGalleyGridRow extends GridRow
{
    /** @var Submission */
    public $_submission;

    /** @var Publication */
    public $_publication;

    /** @var bool */
    public $_isEditable;

    /**
     * Constructor
     *
     * @param Submission $submission
     * @param bool $isEditable
     */
    public function __construct($submission, $publication, $isEditable)
    {
        $this->_submission = $submission;
        $this->_publication = $publication;
        $this->_isEditable = $isEditable;

        parent::__construct();
    }

    //
    // Overridden methods from GridRow
    //
    /**
     * @copydoc GridRow::initialize()
     *
     * @param null|mixed $template
     */
    public function initialize($request, $template = null)
    {
        // Do the default initialization
        parent::initialize($request, $template);

        // Is this a new row or an existing row?
        $rowId = $this->getId();
        if (!empty($rowId) && is_numeric($rowId)) {
            // Only add row actions if this is an existing row
            $router = $request->getRouter();
            $actionArgs = $this->getRequestArgs();
            $actionArgs['representationId'] = $rowId;

            if ($this->_isEditable) {
                // Add row-level actions
                $this->addAction(new LinkAction(
                    'editGalley',
                    new AjaxModal(
                        $router->url($request, null, null, 'editGalley', null, $actionArgs),
                        __('submission.layout.editGalley'),
                        'modal_edit'
                    ),
                    __('grid.action.edit'),
                    'edit'
                ));

                $galley = $this->getData();
                if ($galley->getRemoteUrl() == '') {
                    $this->addAction(new AddFileLinkAction(
                        $request,
                        $this->getSubmission()->getId(),
                        WORKFLOW_STAGE_ID_PRODUCTION,
                        [Role::ROLE_ID_MANAGER, Role::ROLE_ID_SITE_ADMIN, Role::ROLE_ID_SUB_EDITOR, Role::ROLE_ID_ASSISTANT, Role::ROLE_ID_AUTHOR],
                        SubmissionFile::SUBMISSION_FILE_PROOF,
                        ASSOC_TYPE_REPRESENTATION,
                        $rowId,
                        null
                    ));
                }

                $this->addAction(new LinkAction(
                    'deleteGalley',
                    new RemoteActionConfirmationModal(
                        $request->getSession(),
                        __('common.confirmDelete'),
                        __('grid.action.delete'),
                        $router->url($request, null, null, 'deleteGalley', null, $actionArgs),
                        'modal_delete'
                    ),
                    __('grid.action.delete'),
                    'delete'
                ));
            }
        }
    }

    /**
     * Get the submission for this row (already authorized)
     *
     * @return Submission
     */
    public function getSubmission()
    {
        return $this->_submission;
    }

    /**
     * Get the publication for this row (already authorized)
     *
     * @return Publication
     */
    public function getPublication()
    {
        return $this->_publication;
    }

    /**
     * Get the base arguments that will identify the data in the grid.
     *
     * @return array
     */
    public function getRequestArgs()
    {
        return [
            'submissionId' => $this->getSubmission()->getId(),
            'publicationId' => $this->getPublication()->getId(),
        ];
    }
}
