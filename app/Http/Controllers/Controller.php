<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    static public function extractData(Request $request, Object $obj, String | array $name_files = [], $file_path = ""): array
    {
        $data = $request->validated();
        if ($file_path !== "") {
            if (is_array($name_files)) {
                foreach ($name_files as $name_file) {
                    $path = self::extractFile($request, $obj, $name_file, $file_path);
                    if ($path !== "") {
                        $data[$name_file] = $path;
                    }
                }
            }

            return $data;
        }
    }

    static private function extractFile(Request $request, Object $obj, string $name_file = "", $file_path = ""): string
    {
        if (empty($name_file) || empty($file_path)) { {
                return "";
            }
            $file = $request->validated($name_file) ?? null;
            if ($file === null || $file->getError()) {
                return "";
            }
            self::deleteFile($obj, $name_file);
            return $file->store($file_path, 'modules');
        }
    }

    static function deleteFile(Object $obj, string $name_file = ""): void
    {
        if (empty($name_file)) {
            return;
        }
        if ($obj->$name_file) {
            Storage::disk('modules')->delete($obj->$name_file);
        }
    }
}
