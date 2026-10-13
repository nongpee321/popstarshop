<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function index(): View
    {
        $installer = $this->latestPosInstaller();

        return view('portal.index', [
            'companyName' => AppSetting::company('name_th') ?? 'บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด',
            'companyNameEn' => AppSetting::company('name_en') ?? 'PopStar Food Trading Co., Ltd.',
            'logoUrl' => AppSetting::logoUrl(),
            'posInstaller' => $installer,
        ]);
    }

    private function latestPosInstaller(): ?array
    {
        $files = collect(array_merge(
            File::glob(storage_path('app/pos-releases/PopCentral-POS-UAT-*-setup.exe')),
            File::glob(storage_path('app/pos-python-releases/PopCentral-POS-UAT-*-setup.exe')),
        ))
            ->filter(fn (string $path) => is_file($path))
            ->unique()
            ->values()
            ->all();

        if (empty($files)) {
            return null;
        }

        usort($files, static function (string $left, string $right): int {
            preg_match('/-(\d+\.\d+\.\d+)-setup\.exe$/', basename($left), $leftMatch);
            preg_match('/-(\d+\.\d+\.\d+)-setup\.exe$/', basename($right), $rightMatch);
            $versionOrder = version_compare($rightMatch[1] ?? '0.0.0', $leftMatch[1] ?? '0.0.0');

            return $versionOrder !== 0 ? $versionOrder : filemtime($right) <=> filemtime($left);
        });

        $path = $files[0];
        preg_match('/-(\d+\.\d+\.\d+)-setup\.exe$/', basename($path), $matches);

        return [
            'filename' => basename($path),
            'version' => $matches[1] ?? '1.12.23',
            'size_mb' => round(filesize($path) / (1024 * 1024), 1),
            'url' => route('python-pos.download'),
        ];
    }
}
