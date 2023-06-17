<?php
// Romanian (ro)
return [
    'forms' => 3,
    'rule' => '(n == 1 ? 0 : (n == 0 || (n % 100 > 0 && n % 100 < 20)) ? 1 : 2)',
    'func' => function (int $n): int {
        return (int)(($n == 1 ? 0 : (($n == 0 || ($n % 100 > 0 && $n % 100 < 20)) ? 1 : 2)));
    },
];
