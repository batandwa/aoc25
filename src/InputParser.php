<?php
namespace AOC25;

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
        foreach ($this->split as $line) {
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