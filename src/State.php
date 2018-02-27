<?php
namespace AOC25;

class State {
    private $stateConditions;
    private $stateName;

    function __construct(string $stateName) {
        $this->stateName = $stateName;
    }

    public function addStateCondition(StateCondition $sc): void {
        $this->stateConditions[] = $sc;
    }

    public function equals(State $compareState): bool {
        return $compareState->getName() === $this->stateName;
    }

    public function getName(): string {
        return $this->stateName;
    }

    public function findCondition(bool $value): StateCondition {
        foreach ($this->stateConditions as $cond) {
            if ($cond->getIfValue() === $value) {
                return $cond;
            }
        }
        return null;
    }
}