<?php
namespace AOC25;

class StateCondition {
    private $write;
    private $direction;
    private $nextState;
    private $ifValue;

    public function getWrite(): bool {
        return $this->write;
    }

    public function getDirection(): int {
        return $this->direction;
    }

    public function getNextState(): string {
        return $this->nextState;
    }

    public function __construct(bool $ifValue, bool $write, int $direction, string $nextState) {
        if (strlen($nextState) > 1) {
            throw \IllegalArgumentException('AOC25\State indicator can only be a single character');
        }
        $this->write = $write;
        $this->direction = $direction;
        $this->nextState = strtoupper($nextState);
        $this->ifValue = $ifValue;
    }

    public function getIfValue(): bool {
        return $this->ifValue;
    }
}