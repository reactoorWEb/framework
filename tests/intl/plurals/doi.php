<?php
// Dogri (doi)
return [
    'forms' => 2,
    'rule' => '(n != 1)',
    'func' => function (int $n): int {
        return (int)($n != 1);
    },
];
