<?php
require __DIR__.'/vendor/autoload.php';
\ = require_once __DIR__.'/bootstrap/app.php';
\->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

\ = collect(array_merge(
    \Illuminate\Support\Facades\File::glob(storage_path('app/pos-releases/PopCentral-POS-UAT-*-setup.exe')),
    \Illuminate\Support\Facades\File::glob(storage_path('app/pos-python-releases/PopCentral-POS-UAT-*-setup.exe')),
))
    ->filter(fn (string \) => is_file(\))
    ->unique()
    ->values()
    ->all();
usort(\, static function (string \, string \): int {
    preg_match('/-(\d+\.\d+\.\d+)-setup\.exe$/', basename(\), \);
    preg_match('/-(\d+\.\d+\.\d+)-setup\.exe$/', basename(\), \);
    \ = version_compare(\[1] ?? '0.0.0', \[1] ?? '0.0.0');

    return \ !== 0 ? \ : filemtime(\) <=> filemtime(\);
});

echo "Latest Installer: " . basename(\[0] ?? 'NONE') . "\n";
