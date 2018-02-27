<?php
require_once('vendor/autoload.php');

$input = <<<DAT
A011B
A100B
B010A
B111A
DAT;

$input = <<<DAT
A011B
A101C
B000A
B101D
C011D
C111A
D010E
D100D
E011F
E110B
F011A
F111E
DAT;


$stateCollection = (new AOC25\InputParser($input))->build();

$stepsRan = 0;
$targetSteps = 12399302;
//$targetSteps = 6;
$currentStatus = new AOC25\CurrentStatus(new AOC25\Tape(), 0, $stateCollection['A']);

while ($stepsRan < $targetSteps) {
    foreach ($stateCollection as $state) {
        if ($state->equals($currentStatus->getState())) {
            $condition = $state->findCondition($currentStatus->getTape()->valueAt($currentStatus->getPosition()));
            if ($condition == null) {
                throw new RuntimeException('Condition not found for current position. Check blueprint configuration.');
            }

            $currentStatus->transform($condition, $stateCollection);
            break;
        }

        if($stepsRan % 10000 === 0) {
            print '.';
        }
    }
    $stepsRan++;
}

//print $currentStatus->getTape()->toString();
print 'Diagonastic Checksum: ' . $currentStatus->getTape()->diagnosticChecksum() . "\n";
