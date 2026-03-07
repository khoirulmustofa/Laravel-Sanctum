scp /home/nfbs/php/Laravel-Sanctum/.env.service root@192.168.100.125:/home/nfbsb/.env/.env.service


// php artisan queue:work --tries=3 --timeout=60


// php artisan optimize:clear
// php artisan config:clear
// php artisan storage:link

// php artisan db:seed --class=PermissionSeeder
// php artisan make:model Setting -m

// php artisan migrate:fresh --seed

php artisan migrate:rollback --path=/database/migrations/2026_03_07_135205_create_quran_bookmarks_table.php

php artisan migrate --path=/database/migrations/2026_03_07_135205_create_quran_bookmarks_table.php

php artisan migrate:refresh --path=/database/migrations/2026_03_07_135205_create_quran_bookmarks_table.php

php artisan migrate:refresh --seed
