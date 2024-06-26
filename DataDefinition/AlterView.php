<?php

namespace NGFramer\NGFramerPHPSQLServices\DataDefinition;

use NGFramer\NGFramerPHPSQLServices\DataDefinition\Supportive\_DdlView;

class AlterView extends _DdlView
{
    // Construct function from parent class.
    // Location: AlterTable => _DdlTableColumn => _DdlTable.
    // __construct($tableName) function.
    public function __construct(string $viewName)
    {
        parent::__construct($viewName);
        $this->addQueryLog('view', $viewName, 'alterView');
        // Set the action.
        $this->setAction();
    }

    // Set the action for the table.
    protected function setAction($action = null): void
    {
        parent::setAction("alterView");
    }


    // Main function for the class AlterView.
    public function select(string $rawSelectQuery): self
    {
        $this->addToQueryLogDeep('select', $rawSelectQuery);
        return $this;
    }


    // Build function for the class AlterView.
    public function buildQuery(): string
    {
        // Get the queryLog from the class.
        $queryLog = $this->queryLog;
        $viewName = $queryLog['view'];
        // Return the query that has been built.
        return "ALTER VIEW {$viewName} AS {$queryLog['select']}";
    }
}