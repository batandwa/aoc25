<?php
namespace AOC25;

class CurrentStatus {
    private $tape;
    private $position;
    private $state;

    public function __construct(Tape $tape, int $position, State $state) {
        $this->tape = $tape;
        $this->position = $position;
        $this->state = $state;
    }

    public function getTape(): Tape {
        return $this->tape;
    }

    public function setTape(Tape $tape) {
        $this->tape = $tape;
    }

    public function getPosition(): int {
        return $this->position;
    }

    public function setPosition(int $position) {
        $this->position = $position;
    }

    public function getState(): State {
        return $this->state;
    }

    public function setState(State $state) {
        $this->state = $state;
    }

    public function transform(StateCondition $condition, array $stateCollection) {
        $this->tape->writeAt($this->position, $condition->getWrite());
        foreach ($stateCollection as $state) {
            if ($state->getName() === $condition->getNextState()) {
                $this->state = $state;
                break;
            }
        }
        $this->position += $condition->getDirection();
    }
}