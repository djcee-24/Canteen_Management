<?php

return [
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
    'assets_path' => env('FILAMENT_ASSETS_PATH', null),
    'cache_path' => env('FILAMENT_CACHE_PATH', null),
    'livewire_loading_delay' => env('FILAMENT_LIVEWIRE_LOADING_DELAY', 'default'),
];