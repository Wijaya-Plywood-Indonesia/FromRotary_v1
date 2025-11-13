<?php

namespace App\Services;


use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class WatermarkService
{
    public static function addWatermark(string $imagePath, array $info = []): string
    {
        $img = Image::read(storage_path('app/public/' . $imagePath));

        $width = $img->width();
        $height = $img->height();

        // Background semi-transparent di pojok kanan bawah
        $boxHeight = 120;
        // INI BENAR: Gunakan drawRectangle()
        $img->drawRectangle(0, $height - $boxHeight, $width, $height, function ($draw) {
            $draw->background('rgba(0, 0, 0, 0.6)');
        });

        // Logo perusahaan (jika ada)
        $logoPath = public_path('images/logo-watermark.png');
        if (file_exists($logoPath)) {
            $watermark = Image::read($logoPath);
            $watermark->resize(60, 60, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->insert($watermark, 'bottom-left', 15, 15);
        }

        // Text watermark dengan info detail
        $y = $height - 95;
        $lineHeight = 22;

        // Nama perusahaan
        $img->text(config('app.name', 'PT. Kayu Jaya'), 90, $y, function ($font) {
            $font->size(16);
            $font->color('#ffffff');
            $font->align('left');
        });

        $y += $lineHeight;

        // Tanggal & Waktu
        $img->text('Tanggal: ' . now()->format('d/m/Y H:i:s'), 90, $y, function ($font) {
            $font->size(14);
            $font->color('#ffff00');
            $font->align('left');
        });

        $y += $lineHeight;

        // Info tambahan (pegawai, dll)
        if (!empty($info['pegawai'])) {
            $img->text('Pekerja: ' . $info['pegawai'], 90, $y, function ($font) {
                $font->size(14);
                $font->color('#00ff00');
                $font->align('left');
            });
        }

        // Watermark diagonal di tengah (sebagai security)
        $img->text('DOKUMEN RESMI', $width / 2, $height / 2, function ($font) {
            $font->file(public_path('fonts/Arial-Bold.ttf'));
            $font->size(80);
            $font->color('rgba(255, 255, 255, 0.1)');
            $font->align('center');
            $font->valign('middle');
            $font->angle(45);
        });

        // Save dengan kompresi
        $img->save(storage_path('app/public/' . $imagePath), 90);

        return $imagePath;
    }
}