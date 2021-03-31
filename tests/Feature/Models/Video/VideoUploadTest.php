<?php

namespace Tests\Feature\Models\Video;

use App\Exceptions\TestException;
use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Models\Video\BaseVideoTestCase;

class VideoUploadTest extends BaseVideoTestCase
{


    protected function setUp(): void
    {
        parent::setUp();
        \Storage::fake();
        // 
    }

    public function testCreateWithFiles()
    {
        $video = Video::create(
            $this->data + [
                'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                'video_file' => UploadedFile::fake()->create('video.mp4')
            ]
        );
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");
    }

    public function testCreateIfRollbackFiles()
    {
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;
        try {
            Video::create(
                $this->data + [
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'video_file' => UploadedFile::fake()->create('video.mp4')
                ]
            );
        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testUpdateWithFiles()
    {
        $thumbFile = UploadedFile::fake()->image('thumb.jpg');
        $videoFile = UploadedFile::fake()->create('video.mp4');

        $this->video->update($this->data + [
            'thumb_file' => $thumbFile,
            'video_file' => $videoFile
        ]);

        \Storage::assertExists("{$this->video->id}/{$this->video->thumb_file}");
        \Storage::assertExists("{$this->video->id}/{$this->video->video_file}");

        $newVideo = UploadedFile::fake()->create('video.mp4');

        $this->video->update($this->data + [
            'video_file' => $newVideo
        ]);

        \Storage::assertExists("{$this->video->id}/{$thumbFile->hashName()}");
        \Storage::assertExists("{$this->video->id}/{$newVideo->hashName()}");
        \Storage::assertMissing("{$this->video->id}/{$videoFile->hashName()}");
    }

    public function testUpdateIfRollbackFiles()
    {
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });

        $hasError = false;
        try {
            $thumbFile = UploadedFile::fake()->image('thumb.jpg');
            $videoFile = UploadedFile::fake()->create('video.mp4');
            //dd($thumbFile);
            $this->video->update($this->data + [
                'thumb_file' => $thumbFile,
                'video_file' => $videoFile
            ]);
        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }
}