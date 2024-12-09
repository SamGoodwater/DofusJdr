<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class DataService
{
    const DISK_DEFAULT = 'modules';


    static public function extractData(Request $request, Object $obj, array $files = [
        [
            'disk' => 'modules',
            'path_name' => '',
            'file_name' => '',
            'name_bd' => '',
            'is_multiple_files' => false, // si true, alors le fichier est un tableau de fichiers
            'compress' => false
        ]
    ]): array
    {
        $data = $request->validated();
        if ($files !== "" && is_array($files)) {
            foreach ($files as $file) {
                if (is_array($file)) {
                    $file = self::formatArrayFile($file, $obj);
                    if ($file !== false) {
                        $path = self::extractFileAndStore($request, $obj, $file);
                        if ($path !== false) {
                            if (isset($file['is_multiple_files'])) {
                                if ($file['is_multiple_files'] !== true) {
                                    $data[$file['name_bd']] = $path;
                                }
                            }
                        }
                    }
                }
            }

            return $data;
        } else {
            return [];
        }
    }


    static private function formatArrayFile(array $file, $obj): array
    {
        if (is_array($file)) {
            if (isset($file['name_bd'])) {
                return false;
            }
            if (empty($file['name_bd'])) {
                return false;
            }
            $name_db = $file['name_bd'];

            $file[] = [
                'disk' => (string) $file['disk'] ?? self::DISK_DEFAULT,
                'path_name' => (string) $file['path_name'] ?? $obj::class,
                'file_name' => (string) $file['file_name'] ?? '', // laisser vide pour que laravel le génère automatiquement
                'name_bd' => (string) $name_db,
                'is_multiple_files' => (bool) $file['is_multiple_files'] ?? false,
                'compress' => (bool) $file['compress'] ?? false
            ];
        }

        return $file;
    }

    static private function extractFileAndStore(Request $request, Object $obj, array $file): string
    {
        if (empty($file['name_bd']) || empty($file['path_name']) || empty($file['disk'])) {
            return false;
        }

        $input_file = $request->validated($file['name_bd']) ?? null;
        if ($input_file === null || $input_file->getError()) {
            return false;
        }

        self::deleteFile($obj, $file['name_bd']);

        if (!empty($file['file_name'])) {
            $path = $input_file->storeAs($file['path_name'], $file['file_name'], $file['disk']);
        }
        $path = $input_file->store($file['path_name'], $file['disk']);
        if ($file['compress'] === true) {
            self::compressAndConvertImage($path, $file['disk']);
        }

        if ($file['is_multiple_files'] === true) {
            $obj->setPathFiles($path);
        }
        return $path;
    }

    static function deleteFile(Object $obj, string $name_file = "", bool $is_multiple_file = false): bool
    {
        if (empty($name_file)) {
            return false;
        }
        if ($is_multiple_file) {
            $is_success = true;
            $files = $obj->getPathFiles();
            foreach ($files as $file) {
                if (!Storage::disk('modules')->delete($file)) {
                    $is_success = false;
                }
            }
            return $is_success;
        }
        if ($obj->$name_file) {
            return Storage::disk('modules')->delete($obj->$name_file);
        }
    }

    static private function compressAndConvertImage(string $path, string $disk = "modules", int $quality = 70): string
    {
        $manager = new ImageManager($disk);
        $image = $manager->read($path);
        return $image->toWebp($quality);
    }
}
