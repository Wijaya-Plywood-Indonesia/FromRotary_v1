<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Typography\FontFactory;

class WatermarkService
{
    /**
     * Tambahkan watermark ke gambar dengan font proporsional
     *
     * @param string $relativePath Path relatif di storage/app/public/
     * @param array $info Data tambahan (nama_supir, dll)
     * @return string Path relatif (sama jika gagal)
     */
    public static function addWatermark(string $relativePath, array $info = []): string
    {
        try {
            $manager = new ImageManager(new Driver());
            $fullPath = storage_path('app/public/' . $relativePath);

            // 1. Validasi file
            if (!File::exists($fullPath)) {
                Log::warning("Watermark: File tidak ditemukan", ['path' => $fullPath]);
                return $relativePath;
            }

            $mime = mime_content_type($fullPath);
            $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mime, $allowed)) {
                Log::warning("Watermark: Bukan gambar", ['mime' => $mime]);
                return $relativePath;
            }

            // 2. Baca gambar
            $img = $manager->read($fullPath);
            $originalWidth = $img->width();
            $originalHeight = $img->height();

            // 3. Resize jika terlalu besar (> 2000px) untuk performa & visibilitas
            $maxSize = 2000;
            if ($originalWidth > $maxSize || $originalHeight > $maxSize) {
                $img->scaleDown(width: $maxSize, height: $maxSize);
            }

            $width = $img->width();
            $height = $img->height();

            // 4. Hitung ukuran font proporsional
            $fontSize = max(20, (int) ($width / 28)); // ~3.5% dari lebar
            $diagonalFontSize = max(50, (int) ($width / 12)); // ~8% dari lebar
            $lineHeight = (int) ($fontSize * 1.4);

            // 5. Path font
            $fontMedium = public_path('fonts/Roboto-Medium.ttf');
            $logoPath = public_path('images/logo-watermark.png');

            // 6. Background box (pojok kiri bawah)
            $boxWidth = min(500, (int) ($width * 0.4));
            $boxHeight = 130;
            $boxX = 15;
            $boxY = $height - $boxHeight - 15;

            $img->drawRectangle($boxX, $boxY, function ($rect) use ($boxWidth, $boxHeight) {
                $rect->size($boxWidth, $boxHeight);
                $rect->background('rgba(0, 0, 0, 0.65)');
                $rect->border('#ffffff', 1);
            });

            // 7. Logo (jika ada)
            $logoOffsetX = 25;
            $logoOffsetY = 25;
            $hasLogo = false;

            if (File::exists($logoPath)) {
                try {
                    $logo = $manager->read($logoPath);
                    $logo->scaleDown(width: 70, height: 70);
                    $img->place($logo, 'bottom-left', $logoOffsetX, $logoOffsetY);
                    $hasLogo = true;
                } catch (\Exception $e) {
                    Log::warning("Watermark: Gagal load logo", ['error' => $e->getMessage()]);
                }
            }

            // 8. Teks mulai dari...
            $textX = $hasLogo ? 100 : 30;
            $textY = $boxY + 35;

            // Helper untuk text
            $addText = function ($text, &$y) use ($img, $textX, $fontMedium, $fontSize, $lineHeight) {
                $img->text($text, $textX, $y, function (FontFactory $font) use ($fontMedium, $fontSize) {
                    if (File::exists($fontMedium)) {
                        $font->file($fontMedium);
                    }
                    $font->size($fontSize);
                    $font->color('#ffffff');
                    $font->align('left');
                    $font->valign('top');
                });
                $y += $lineHeight;
            };

            // Tanggal & Waktu
            $addText('Tanggal: ' . now()->format('d/m/Y H:i:s'), $textY);

            // Nama Supir
            if (!empty($info['nama_supir'])) {
                $addText('Supir: ' . $info['nama_supir'], $textY);
            }

            // 9. Watermark Diagonal (Security)
            $diagonalText = $info['diagonal_text'] ?? 'DOKUMEN RESMI';
            $diagonalFont = $fontMedium;

            if (File::exists($diagonalFont)) {
                $img->text($diagonalText, $width / 2, $height / 2, function (FontFactory $font) use ($diagonalFont, $diagonalFontSize) {
                    $font->file($diagonalFont);
                    $font->size($diagonalFontSize);
                    $font->color('rgba(255, 255, 255, 0.22)');
                    $font->align('center');
                    $font->valign('middle');
                    $font->angle(45);
                });

                // Overlay tipis untuk efek "cap"
                $img->drawRectangle(0, 0, function ($rect) use ($width, $height) {
                    $rect->size($width, $height);
                    $rect->background('rgba(255, 255, 255, 0.02)');
                });
            }

            // 10. Simpan dengan kualitas optimal
            $img->save($fullPath, quality: 92);

            Log::info("Watermark berhasil ditambahkan", [
                'path' => $relativePath,
                'size' => "{$width}x{$height}",
                'font_size' => $fontSize
            ]);

            return $relativePath;

        } catch (\Throwable $e) {
            Log::error("Watermark gagal", [
                'path' => $relativePath ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $relativePath;
        }
    }
}