<?php
// Icelandic (is)
return [
    'forms' => 2,
    'rule' => '(n % 10 != 1 || n % 100 == 11)',
    'func' => function (int $n): int {
        return (int)($n % 10 != 1 || $n % 100 == 11);
    },
];
