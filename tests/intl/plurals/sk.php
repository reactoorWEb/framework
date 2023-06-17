<?php
// Slovak (sk)
return [
    'forms' => 3,
    'rule' => '(n == 1) ? 0 : (n >= 2 && n <= 4) ? 1 : 2',
    'func' => function (int $n): int {
        return (int)(($n == 1) ? 0 : (($n >= 2 && $n <= 4) ? 1 : 2));
    },
];
