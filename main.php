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
            if($cond->getIfValue() === $value) {
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
        if(strlen($nextState) > 1) {
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
        if(!isset($this->slots[$position])) {
            $this->slots[$position] = false;
        }
        return $this->slots[$position];
    }

    public function toString(): string {
        $output = $this->slots;
        ksort($output);
        $output = array_map(function($val) { return $val ? '1' : '0'; }, $output);
        return implode(' ', $output);
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
//        $this->tape[$this->position] = $condition->getWrite();
        $this->tape->writeAt($this->position, $condition->getWrite());
        foreach($stateCollection as $state) {
            if($state->getName() === $condition->getNextState()) {
                $this->state = $state;
                break;
            }
        }
//        $this->state = $condition->getNextState();
        $this->position += $condition->getDirection();
    }
}


$stateA = new State('A');
$stateA->addStateCondition(new StateCondition(0, 1, Direction::RIGHT, 'B'));
$stateA->addStateCondition(new StateCondition(1, 0, Direction::LEFT, 'B'));

$stateB = new State('B');
$stateB->addStateCondition(new StateCondition(0, 1, Direction::LEFT, 'A'));
$stateB->addStateCondition(new StateCondition(1, 1, Direction::RIGHT, 'A'));

$stateCollection = [$stateA, $stateB];

//$tape = new Tape();
//$tapePosition = 0;
//$currentState = $stateA;
$stepsRan = 0;
$currentStatus = new CurrentStatus(new Tape(), 0, $stateA);

while($stepsRan < 6) {
    foreach($stateCollection as $state) {
        if($state->equals($currentStatus->getState())) {
            $condition = $state->findCondition($currentStatus->getTape()->valueAt($currentStatus->getPosition()));
            if($condition == null) {
                throw new RuntimeException('Condition not found for current position. Check blueprint configuration.');
            }

            $currentStatus->transform($condition, $stateCollection);
            break;
        }
    }
    $stepsRan++;
}

print $currentStatus->getTape()->toString();

print "\n";
exit;











$str = 'twelve billion people know iPhone has two hundred and thirty thousand, seven hundred and eighty-three apps as well as over one million units sold';

function strlen_sort($a, $b)
{
    if(strlen($a) > strlen($b))
    {
        return -1;
    }
    else if(strlen($a) < strlen($b))
    {
        return 1;
    }
    return 0;
}

$keys = array(
    'one' => '1', 'two' => '2', 'three' => '3', 'four' => '4', 'five' => '5', 'six' => '6', 'seven' => '7', 'eight' => '8', 'nine' => '9',
    'ten' => '10', 'eleven' => '11', 'twelve' => '12', 'thirteen' => '13', 'fourteen' => '14', 'fifteen' => '15', 'sixteen' => '16', 'seventeen' => '17', 'eighteen' => '18', 'nineteen' => '19',
    'twenty' => '20', 'thirty' => '30', 'forty' => '40', 'fifty' => '50', 'sixty' => '60', 'seventy' => '70', 'eighty' => '80', 'ninety' => '90',
    'hundred' => '100', 'thousand' => '1000', 'million' => '1000000', 'billion' => '1000000000'
);


preg_match_all('#((?:^|and|,| |-)*(\b' . implode('\b|\b', array_keys($keys)) . '\b))+#i', $str, $tokens);
//print_r($tokens); exit;
$tokens = $tokens[0];
usort($tokens, 'strlen_sort');

foreach($tokens as $token)
{
    $token = trim(strtolower($token));
    preg_match_all('#(?:(?:and|,| |-)*\b' . implode('\b|\b', array_keys($keys)) . '\b)+#', $token, $words);
    $words = $words[0];
    //print_r($words);
    $num = '0'; $total = 0;
    foreach($words as $word)
    {
        $word = trim($word);
        $val = $keys[$word];
        //echo "$val\n";
        if(bccomp($val, 100) == -1)
        {
            $num = bcadd($num, $val);
            continue;
        }
        else if(bccomp($val, 100) == 0)
        {
            $num = bcmul($num, $val);
            continue;
        }
        $num = bcmul($num, $val);
        $total = bcadd($total, $num);
        $num = '0';
    }
    $total = bcadd($total, $num);
    echo "$total:$token\n";
    $str = preg_replace("#\b$token\b#i", number_format($total), $str);
}

echo "\n$str\n";