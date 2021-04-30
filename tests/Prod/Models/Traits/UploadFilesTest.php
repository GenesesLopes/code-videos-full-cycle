<?php

namespace Tests\Prod\Models\Traits;

use Illuminate\Http\UploadedFile;
use Tests\Stubs\Models\UploadFileStub;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\TestProd;
use Tests\Traits\TestStorages;

class UploadFilesTest extends TestCase
{

    use TestStorages, TestProd;
    /**
     *
     * @var UploadFileStub
     */
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skipTestIfNotProd();
        $this->obj = new UploadFileStub;
        \Config::set('filesystems.default', 'gcs');
        $this->deleteAllFiles();
    }

    public function testUploadFile()
    {
        // $this->markTestSkipped('Testes de produção');
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadFiles()
    {
        $files = [];
        for ($i = 1; $i <= 2; $i++)
            array_push($files, UploadedFile::fake()->create("video{$i}.mp4"));
        $this->obj->uploadFiles($files);
        foreach ($files as $file) {
            Storage::assertExists("1/{$file->hashName()}");
        }
    }


    public function testDeleteFile()
    {
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file->hashName());
        Storage::assertMissing("1/{$file->hashName()}");

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file);
        Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteOldFiles()
    {
        $files = [];
        for ($i = 1; $i <= 2; $i++)
            array_push($files, UploadedFile::fake()->create("video{$i}.mp4")->size(1));
        $this->obj->uploadFiles($files);
        $this->obj->deleteOldFiles();
        $this->assertCount(2, Storage::allFiles());

        $this->obj->oldFiles = [$files[1]->hashName()];
        $this->obj->deleteOldFiles();
        foreach ($files as $key => $file) {
            if ($key)
                Storage::assertMissing("1/{$file->hashName()}");
            else
                Storage::assertExists("1/{$file->hashName()}");
        }
    }

    public function testDeleteFiles()
    {
        $files = [];
        for ($i = 1; $i <= 2; $i++)
            array_push($files, UploadedFile::fake()->create("video{$i}.mp4"));
        $this->obj->uploadFiles($files);
        $this->obj->deleteFiles([$files[0]->hashName(), $files[1]]);
        foreach ($files as $file) {
            Storage::assertMissing("1/{$file->hashName()}");
        }
    }
}