<?php

/**
 * @file controllers/grid/pubIds/PubIdExportRepresentationsListGridCellProvider.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PubIdExportRepresentationssListGridCellProvider
 * @ingroup controllers_grid_pubIds
 *
 * @brief Class for a cell provider that can retrieve labels from representations with pub ids
 */

namespace APP\controllers\grid\pubIds;

use APP\facades\Repo;
use PKP\controllers\grid\DataObjectGridCellProvider;
use PKP\controllers\grid\GridHandler;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\RedirectAction;

class PubIdExportRepresentationsListGridCellProvider extends DataObjectGridCellProvider
{
    /** @var ImportExportPlugin */
    public $_plugin;

    /**
     * Constructor
     *
     * @param null|mixed $authorizedRoles
     */
    public function __construct($plugin, $authorizedRoles = null)
    {
        $this->_plugin = $plugin;
        if ($authorizedRoles) {
            $this->_authorizedRoles = $authorizedRoles;
        }
        parent::__construct();
    }

    //
    // Template methods from GridCellProvider
    //
    /**
     * Get cell actions associated with this row/column combination
     *
     * @copydoc GridCellProvider::getCellActions()
     */
    public function getCellActions($request, $row, $column, $position = GridHandler::GRID_ACTION_POSITION_DEFAULT)
    {
        $galley = $row->getData();
        $columnId = $column->getId();
        assert(is_a($galley, 'Galley') && !empty($columnId));

        $publication = Repo::publication()->get($galley->getData('publicationId'));
        $submission = Repo::submission()->get($publication->getData('submissionId'));
        switch ($columnId) {
            case 'title':
                $this->_titleColumn = $column;
                $title = $submission->getLocalizedTitle();
                if (empty($title)) {
                    $title = __('common.untitled');
                }
                $authorsInTitle = $submission->getShortAuthorString();
                $title = $authorsInTitle . '; ' . $title;
                return [
                    new LinkAction(
                        'itemWorkflow',
                        new RedirectAction(
                            Repo::submission()->getWorkflowUrlByUserRoles($submission)
                        ),
                        htmlspecialchars($title)
                    )
                ];
            case 'status':
                $status = $galley->getData($this->_plugin->getDepositStatusSettingName());
                $statusNames = $this->_plugin->getStatusNames();
                $statusActions = $this->_plugin->getStatusActions($submission);
                if ($status && array_key_exists($status, $statusActions)) {
                    assert(array_key_exists($status, $statusNames));
                    return [
                        new LinkAction(
                            'edit',
                            new RedirectAction(
                                $statusActions[$status],
                                '_blank'
                            ),
                            htmlspecialchars($statusNames[$status])
                        )
                    ];
                }
        }
        return parent::getCellActions($request, $row, $column, $position);
    }

    /**
     * Extracts variables for a given column from a data element
     * so that they may be assigned to template before rendering.
     *
     * @copydoc DataObjectGridCellProvider::getTemplateVarsFromRowColumn()
     */
    public function getTemplateVarsFromRowColumn($row, $column)
    {
        $submissionGalley = $row->getData();
        $columnId = $column->getId();
        assert(is_a($submissionGalley, 'Galley') && !empty($columnId));

        switch ($columnId) {
            case 'id':
                return ['label' => $submissionGalley->getId()];
            case 'title':
                return ['label' => ''];
            case 'galley':
                return ['label' => $submissionGalley->getGalleyLabel()];
            case 'pubId':
                return ['label' => $submissionGalley->getStoredPubId($this->_plugin->getPubIdType())];
            case 'status':
                $status = $submissionGalley->getData($this->_plugin->getDepositStatusSettingName());
                $statusNames = $this->_plugin->getStatusNames();
                $statusActions = $this->_plugin->getStatusActions($submissionGalley);
                if ($status) {
                    if (array_key_exists($status, $statusActions)) {
                        $label = '';
                    } else {
                        assert(array_key_exists($status, $statusNames));
                        $label = $statusNames[$status];
                    }
                } else {
                    $label = $statusNames[EXPORT_STATUS_NOT_DEPOSITED];
                }
                return ['label' => $label];
        }
    }
}
