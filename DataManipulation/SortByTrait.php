<?php

namespace NGFramer\NGFramerPHPSQLBuilder\DataManipulation;

Trait SortByTrait
{
    // Abstract function used in the class.
    abstract function addToQueryLogDeepArray(mixed ... $arguments): void;




    // Main function for the class.
    public function sortBy(mixed ...$sortInstructions): self
    {
        // If the sorting instructions has not been passed.
        if (empty($sortInstructions)) {
            throw new \InvalidArgumentException('At least one sorting instruction must be provided.');
        }

        // If the sorting instructions are passed as two strings.
        if (count($sortInstructions) == 2 && is_string($sortInstructions[0]) || is_string($sortInstructions[1])) {
            $this->addToQueryLogDeepArray('sortBy', ['field' => $sortInstructions[0], 'order' => $sortInstructions[1]]);
            return $this;
        }

        // If the sorting instructions are passed as an array.
        foreach ($sortInstructions as $sortInstruction){
            // Sort Instruction is an array.
            if (is_array($sortInstruction)) {
                $this ->addToQueryLogDeepArray('sortBy', ['field' => $sortInstruction['field'] ?? $sortInstruction[0], 'order' => $sortInstruction['order'] ?? $sortInstruction[1]]);
                return $this;
            }
            // Sort Instruction is not array.
            else {
                throw new \InvalidArgumentException('Invalid sorting instruction format. Expected 2 arguments.');
            }
        }

        // If anything reaches to this point, throw an exception.
        throw new \InvalidArgumentException('Something went wrong, please check the format of sorting instructions and run the function again.');
    }

    public function addAsc(string $columnName): self
    {
        $this->sortBy($columnName, 'ASC');
        return $this;
    }

    public function addDesc(string $columnName): self
    {
        $this->sortBy($columnName, 'DESC');
        return $this;
    }
}