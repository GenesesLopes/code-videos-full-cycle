<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Video;

use App\Exceptions\TestException;
use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Models\Video\BaseVideoTestCase;

class VideoUploadTest extends BaseVideoTestCase
{



    public function testCreateWithFiles()
    {
        \Storage::fake();
        $video = Video::create(
            $this->data + [
                'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                'video_file' => UploadedFile::fake()->create('video.mp4'),
                'banner_file' => UploadedFile::fake()->create('banner.jpg'),
                'trailer_file' => UploadedFile::fake()->create('trailer.mp4')
            ]
        );
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
    }

    public function testCreateIfRollbackFiles()
    {
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;
        try {
            Video::create(
                $this->data + [
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'video_file' => UploadedFile::fake()->create('video.mp4'),
                    'banner_file' => UploadedFile::fake()->create('banner.jpg'),
                    'trailer_file' => UploadedFile::fake()->create('trailer.mp4')
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
        \Storage::fake();
        $thumbFile = UploadedFile::fake()->image('thumb.jpg');
        $videoFile = UploadedFile::fake()->create('video.mp4');
        $bannerFile = UploadedFile::fake()->create('banner.jpg');
        $trailerFile = UploadedFile::fake()->create('trailer.mp4');

        $this->video->update($this->data + [
            'thumb_file' => $thumbFile,
            'video_file' => $videoFile,
            'banner_file' => $bannerFile,
            'trailer_file' => $trailerFile
        ]);

        \Storage::assertExists("{$this->video->id}/{$this->video->thumb_file}");
        \Storage::assertExists("{$this->video->id}/{$this->video->video_file}");
        \Storage::assertExists("{$this->video->id}/{$this->video->banner_file}");
        \Storage::assertExists("{$this->video->id}/{$this->video->trailer_file}");

        $newVideo = UploadedFile::fake()->create('video.mp4');

        $this->video->update($this->data + [
            'video_file' => $newVideo
        ]);

        \Storage::assertExists("{$this->video->id}/{$thumbFile->hashName()}");
        \Storage::assertExists("{$this->video->id}/{$bannerFile->hashName()}");
        \Storage::assertExists("{$this->video->id}/{$trailerFile->hashName()}");
        \Storage::assertExists("{$this->video->id}/{$newVideo->hashName()}");
        \Storage::assertMissing("{$this->video->id}/{$videoFile->hashName()}");
    }

    public function testUpdateIfRollbackFiles()
    {
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });

        $hasError = false;
        try {
            $thumbFile = UploadedFile::fake()->image('thumb.jpg');
            $videoFile = UploadedFile::fake()->create('video.mp4');
            $bannerFile = UploadedFile::fake()->create('banner.jpg');
            $trailerFile = UploadedFile::fake()->create('trailer.mp4');
            //dd($thumbFile);
            $this->video->update($this->data + [
                'thumb_file' => $thumbFile,
                'video_file' => $videoFile,
                'banner_file' => $bannerFile,
                'trailer_file' => $trailerFile
            ]);
        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testFileUrlWithLocalDriver()
    {
        $fileFields = [];
        foreach (Video::$fileFields as $field) {
            $fileFields[$field] = "$field.test";
        }

        $video = factory(Video::class)->create($fileFields);
        $localDrive = config('filesystems.default');
        $baseUrl = config('filesystems.disks.' . $localDrive)['url'];
        foreach ($fileFields as $field => $value) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/$video->id/$value", $fileUrl);
        }
    }

    public function testFileUrlWithGcsDriver()
    {
        $fileFields = [];
        foreach (Video::$fileFields as $field) {
            $fileFields[$field] = "$field.test";
        }

        $video = factory(Video::class)->create($fileFields);
        $baseUrl = config('filesystems.disks.gcs.storage_api_uri');
        \Config::set('filesystems.default', 'gcs');
        foreach ($fileFields as $field => $value) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/$video->id/$value", $fileUrl);
        }
    }

    public function testFileUrlsIfNullWhenFieldsAreNull()
    {
        $video = factory(Video::class)->create();
        foreach (Video::$fileFields as $field) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertNull($fileUrl);
        }
    }
}