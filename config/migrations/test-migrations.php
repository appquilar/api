<?php

// config/migrations/test-migrations.php

return [
    // dónde están tus migraciones normales
    'migrations_paths' => [
        'DoctrineMigrations' => __DIR__ . '/../../migrations',
    ],

    // 🔴 lo que queríamos cambiar
    'transactional'    => false,
    'all_or_nothing'   => false,
];
