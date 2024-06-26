<?php

namespace NGFramer\NGFramerPHPSQLServices\DataManipulation;

use NGFramer\NGFramerPHPExceptions\exceptions\SqlBuilderException;
use NGFramer\NGFramerPHPSQLServices\DataManipulation\Supportive\_DmlTable;

class UpdateTable extends _DmlTable
{
    // Variables to be used in the class.
    private int $counter = 0;


    // Use the following trait to access the functions.
    use WhereTrait, LimitTrait {
        WhereTrait::buildQuery as whereBuild;
        LimitTrait::buildQuery as buildLimit;
    }


    // Constructor for the UpdateTable class.
    public function __construct($tableName, $data)
    {
        // Set the table name using parent constructor.
        parent::__construct($tableName);
        // Add the entry to the query log.
        $this->addQueryLog('table', $tableName, 'updateData');
        // Call the update function.
        $this->update($data);
        // Set the action.
        $this->setAction();
    }


    // Set the action for the table.
    protected function setAction($action = null): void
    {
        parent::setAction("updateData");
    }


    public function update(array $data): void
    {
        // Check if the data is an associative array.
        if ($this->isAssocArray($data)) {
            // Single update
            foreach ($data as $key => $value) {
                $this->addToQueryLogDeep('data', $key, 'field', $key);
                $this->addToQueryLogDeep('data', $key, 'value', $value);
            }
        } else {
            // Multiple updates
            foreach ($data as $element) {
                if (is_array($element) and $this->isAssocArray($element)) {
                    $this->addToQueryLogDeep('data', $this->counter, 'field', $element['field']);
                    $this->addToQueryLogDeep('data', $this->counter, 'value', $element['value']);
                } else if (is_array($element) and !$this->isAssocArray($element)) {
                    $this->addToQueryLogDeep('data', $this->counter, 'field', $element[0]);
                    $this->addToQueryLogDeep('data', $this->counter, 'value', $element[1]);
                } else {
                    throw new SqlBuilderException('Data must be an array/s.', 0, null, 500, ['error_type'=>'dmlUpdate_invalid_data']);
                }
                $this->counter++;
            }
        }
    }


    // Go direct function for where conditions.
    public function goDirect(): self
    {
        // Get the goDirect function from parent.
        parent::goDirect();
        // Return instance for object chaining.
        return $this;
    }


    // Builder function for the class.
    public function buildQuery(): string
    {
        // Get the queryLog initially to process.
        $queryLog = $this->getQueryLog();
        // Start building the update query.
        $query = 'UPDATE ' . $queryLog['table'] . ' SET ';

        // Build the SET clause.
        $setData = [];

        // Check if the data is empty.
        if (empty($queryLog['data'])) {
            throw new SqlBuilderException('InvalidArgumentException, No data to update.', 0, null, 500, ['error_type'=>'dmlUpdate_invalid_data']);
        }

        // Loop through the data to find the values to update.
        foreach ($queryLog['data'] as $elements) {
            $key = $elements['field'] ?? $elements[0];
            $value = $elements['value'] ?? $elements[1];
            // Check method of execution and process the data accordingly.
            // For the default method, prepared statements.
            if (!$this->isGoDirect()) {
                $bindIndex = $this->getBindIndexStarter();
                $this->updateBindValues($key . $bindIndex, $value);
                $setData[] = "$key = :$key$bindIndex";
            } // For the direct method, add the values directly.
            else {
                $value = $this->sanitizeValue($value);
                $setData[] = "$key = '$value'";
            }
        }
        // Add the SET clause to the query.
        $query .= implode(', ', $setData);
        // Add the WHERE clause if present.
        $query .= $this->whereBuild();
        $query .= $this->buildLimit();
        // Return the query.
        return $query;
    }
}