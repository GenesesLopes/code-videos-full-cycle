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

    public function testDeleteFile()
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file->hashName());
        Storage::assertMissing("1/{$file->hashName()}");

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file);
        Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteFiles()
    {
        Storage::fake();
        $files = [];
        for ($i = 1; $i <= 2; $i++)
            array_push($files, UploadedFile::fake()->create("video{$i}.mp4"));
        $this->obj->uploadFiles($files);
        $this->obj->deleteFiles([$files[0]->hashName(), $files[1]]);
        foreach ($files as $file) {
            Storage::assertMissing("1/{$file->hashName()}");
        }
    }


    public function testExtractFiles()
    {
        $attributes = [];
        $files = UploadFileStub::extractFiles($attributes);
        $this->assertEquals($attributes, $files);

        $attributes = ['file1' => 'teste'];
        $files = UploadFileStub::extractFiles($attributes);
        $this->assertCount(1, $attributes);
        $this->assertEquals(['file1' => 'teste'], $attributes);
        $this->assertCount(0, $files);

        $attributes = ['file1' => 'teste', 'file2' => 'teste'];
        $files = UploadFileStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => 'teste', 'file2' => 'teste'], $attributes);
        $this->assertCount(0, $files);

        $file1 = UploadedFile::fake()->create('video1.mp4');
        $attributes = ['file1' => $file1, 'other' => 'test'];
        $files = UploadFileStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => $file1->hashName(), 'other' => 'test'], $attributes);
        $this->assertEquals([$file1], $files);

        $file2 = UploadedFile::fake()->create('video1.mp4');
        $attributes = ['file1' => $file1, 'file2' => $file2, 'other' => 'test'];
        $files = UploadFileStub::extractFiles($attributes);
        $this->assertCount(3, $attributes);
        $this->assertEquals(['file1' => $file1->hashName(), 'file2' => $file2->hashName(), 'other' => 'test'], $attributes);
        $this->assertEquals([$file1, $file2], $files);
    }
}