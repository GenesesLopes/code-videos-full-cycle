<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase\Models;
use Tests\Stubs\Models\UploadFileStub;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class UploadFilesUnitTest extends TestCase
{

    /**
     *
     * @var UploadFileStub
     */
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFileStub;
    }

    public function testUploadFile()
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadFiles()
    {
        Storage::fake();
        $files = [];
        for ($i = 1; $i <= 2; $i++)
            array_push($files, UploadedFile::fake()->create("video{$i}.mp4"));
        $this->obj->uploadFiles($files);
        foreach ($files as $file) {
            Storage::assertExists("1/{$file->hashName()}");
        }
    }
}