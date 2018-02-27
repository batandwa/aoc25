<?php
namespace AOC25;

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

    public function diagnosticChecksum(): int {
        return array_reduce($this->slots, function($a, $b) {
            return $a + $b;
        }, 0);
    }
}