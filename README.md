# Files

## Install

    php artisan migrate
    php artisan vendor:publish --provider="MBober35\Fileable\ServiceProvider" --tag=public
    php artisan fileable
    php artisan storage:link
    FILESYSTEM_DRIVER=public - что бы был доступ к файлам из паблика