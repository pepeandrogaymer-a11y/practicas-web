<?php

return [
    // Rangos permitidos
    'max_faltas' => 100,
    'min_calificacion' => 0,
    'max_calificacion' => 10,

    // Calificación mínima para cada equivalencia (de mayor a menor)
    'minimos' => [
        'AU' => 10,
        'DE' => 9,
        'SA' => 8,
    ],

    'nombres' => [
        'NA' => 'No Acreditado',
        'SA' => 'Suficiente',
        'DE' => 'Destacado',
        'AU' => 'Autónomo',
    ],
];