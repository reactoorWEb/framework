<?php
// Latvian (lv)
return [
    'forms' => 3,
    'rule' => '(n % 10 == 1 && n % 100 != 11 ? 0 : n != 0 ? 1 : 2)',
    'func' => function (int $n): int {
        return (int)(($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n != 0 ? 1 : 2)));
    },
];
