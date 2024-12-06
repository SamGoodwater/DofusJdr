<?php

namespace App\Rules;

class FileRules
{

    const IMAGE = "jpeg,jpg,png,gif,svg,webp,avif,apng,bmp,ico,tiff";
    const VIDEO = "mp4,webm,3gp,fl,avi,mkv,mov,wmv,mpg,mpeg,vob,ogv,ogg,drc";
    const AUDIO = "wav,mp3,ogg,flac,alac,aac,opus,webm,m4a,3gp,amr";
    const DOCUMENT = "pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,odt,ods,odp";

    const TYPE_IMAGE = "image";
    const TYPE_VIDEO = "video";
    const TYPE_AUDIO = "audio";
    const TYPE_DOCUMENT = "document";

    /**
     * Get the validation rules for file MIME types.
     *
     * @return array
     */
    public static function rules(string | array $types, int $max = 20000, bool $required = false, bool $nullable = true): array
    {
        return [
            "file" => ["file", "mimes:" . self::getMimeTypes($types), "max:$max", $required ? "required" : "", $nullable ? "nullable" : ""],
        ];
    }

    /**
     * Get the MIME types for the given file type.
     *
     * @param string $type
     * @return string
     */
    private static function getMimeTypes(string | array $type = self::TYPE_IMAGE): string
    {
        if (is_array($type)) {
            $types = [];
            foreach ($type as $t) {
                $types[] = self::getMimeTypes($t);
            }
            return implode(",", $types);
        } else {
            switch ($type) {
                case self::TYPE_IMAGE:
                    return self::IMAGE;
                case self::TYPE_VIDEO:
                    return self::VIDEO;
                case self::TYPE_AUDIO:
                    return self::AUDIO;
                case self::TYPE_DOCUMENT:
                    return self::DOCUMENT;
                default:
                    return "";
            }
        }
    }
}
