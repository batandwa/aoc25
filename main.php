<?php

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
    }
}

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
            throw \IllegalArgumentException('State indicator can only be a single character');
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

abstract class Direction {
    const LEFT = -1;
    const RIGHT = 1;
}

class Tape {
    private $slots;

    public function valueAt(int $position): bool {
        if (!isset($this->slots[$position])) {
            $this->slots[$position] = false;
        }
        return $this->slots[$position];
    }

    public function toString(): string {
        $slots = $this->slots;
        ksort($slots);
        $slots = array_map(function ($val) {
            return $val ? '1' : '0';
        }, $slots);
        $keys = array_map(function ($val) {
            return str_pad($val, 3, ' ', STR_PAD_LEFT);
        }, array_keys($slots));
        $values = array_map(function ($val) {
            return str_pad($val, 3, ' ', STR_PAD_LEFT);
        }, $slots);

        $output = implode(' ', $keys) . "\n" . implode(' ', $values) . "\n";
        return $output;
    }

    public function writeAt(int $position, bool $getWrite): void {
        $this->slots[$position] = $getWrite;
    }
}

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

class InputParser {
    private $split;
    private $stateCollection = [];

    public function __construct(string $input) {
        $this->split = $this->explode($input);
    }

    private function explode(string $input) {
        return array_map('trim', explode("\n", $input));
    }

    public function build() {
        foreach($this->split as $line) {
            $state = isset($this->stateCollection[$line[0]]) ? $this->stateCollection[$line[0]] : new State($line[0]);
            $stateCondition = new StateCondition(
                (bool)$line[1], $line[2], $line[3] == 1 ? Direction::RIGHT : Direction::LEFT, $line[4]
            );
            $state->addStateCondition($stateCondition);
            $this->stateCollection[$line[0]] = $state;
        }

        return $this->stateCollection;
    }
}

$input = <<<DAT
A011B
A100B
B010A
B111A
DAT;

$stateCollection = (new InputParser($input))->build();

$stepsRan = 0;
$currentStatus = new CurrentStatus(new Tape(), 0, $stateCollection['A']);

while ($stepsRan < 6) {
    foreach ($stateCollection as $state) {
        if ($state->equals($currentStatus->getState())) {
            $condition = $state->findCondition($currentStatus->getTape()->valueAt($currentStatus->getPosition()));
            if ($condition == null) {
                throw new RuntimeException('Condition not found for current position. Check blueprint configuration.');
            }

            $currentStatus->transform($condition, $stateCollection);
            break;
        }
    }
    $stepsRan++;
}

print $currentStatus->getTape()->toString();
