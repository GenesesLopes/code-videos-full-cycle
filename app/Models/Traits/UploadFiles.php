<?php

namespace App\Models\Traits;

use Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

trait UploadFiles
{
    public $oldFiles = [];

    protected abstract function uploadDir();

    public static function bootUploadFiles()
    {
        static::updating(function (Model $model) {
            $fildesUpdated = array_keys($model->getDirty());
            $filesUpdated = array_intersect($fildesUpdated, self::$fileFields);
            $filesFiltered = Arr::where($filesUpdated, function ($fileField) use ($model) {
                return $model->getOriginal($fileField);
            });
            $model->oldFiles = array_map(function ($fileField) use ($model) {
                return $model->getOriginal($fileField);
            }, $filesFiltered);
        });
    }

    public function relativeFilePath($value)
    {
        return "{$this->uploadDir()}/{$value}";
    }

    public function getFileUrl($fileName)
    {
        return \Storage::url($this->relativeFilePath($fileName));
    }

    /**
     * @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }

    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }
    /** @param string|UploadedFile $file */
    public function deleteFile($file)
    {
        $fileName = $file instanceof UploadedFile ? $file->hashName() : $file;
        \Storage::delete("{$this->uploadDir()}/{$fileName}");
    }

    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach (self::$fileFields as $file) {
            if (isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile) {
                $files[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }
        return $files;
    }
}