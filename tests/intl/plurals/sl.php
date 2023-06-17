<?php
// Slovenian (sl)
return [
    'forms' => 4,
    'rule' => '(n % 100 == 1 ? 0 : n % 100 == 2 ? 1 : n % 100 == 3 || n % 100 == 4 ? 2 : 3)',
    'func' => function (int $n): int {
        return (int)(($n % 100 == 1 ? 0 : ($n % 100 == 2 ? 1 : ($n % 100 == 3 || $n % 100 == 4 ? 2 : 3))));
    },
];
