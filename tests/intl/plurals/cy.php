<?php
// Welsh (cy)
return [
    'forms' => 4,
    'rule' => '(n == 1) ? 0 : (n == 2) ? 1 : (n != 8 && n != 11) ? 2 : 3',
    'func' => function (int $n): int {
        return (int)(($n == 1) ? 0 : (($n == 2) ? 1 : (($n != 8 && $n != 11) ? 2 : 3)));
    },
];
