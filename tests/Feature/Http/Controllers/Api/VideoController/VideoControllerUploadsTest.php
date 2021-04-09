<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Video;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoControllerUploadsTest extends BaseVideoControllerTestCase
{
    use TestUploads, TestValidations;

    public function testInvalidationVideoFileField()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            Video::VIDEO_FILE_MAX_SIZE,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testInvalidationThumbFileField()
    {

        $this->assertInvalidationFile(
            'thumb_file',
            'jpg',
            Video::THUMB_FILE_MAX_SIZE,
            'image'
        );
    }

    public function testInvalidationBannerFileField()
    {

        $this->assertInvalidationFile(
            'banner_file',
            'jpg',
            Video::BANNER_FILE_MAX_SIZE,
            'image'
        );
    }

    public function testInvalidationTrailerFileField()
    {

        $this->assertInvalidationFile(
            'trailer_file',
            'mp4',
            Video::TRAILER_FILE_MAX_SIZE,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }
    public function testStoreWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'POST',
            $this->routeStore(),
            $this->sendData + $files
        );
        $response->assertStatus(201);
        $this->assertFilesOnPersist($response, $files);
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();
        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendData + $files
        );
        $response->assertStatus(200);
        $this->assertFilesOnPersist($response, $files);

        $newFiles = [
            'thumb_file' => UploadedFile::fake()->create('thumb_file.jpg'),
            'video_file' => UploadedFile::fake()->create('video_file.mp4')
        ];

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendData + $newFiles
        );

        $response->assertStatus(200);

        $this->assertFilesOnPersist(
            $response,
            \Arr::except($files, ['thumb_file', 'video_file']) + $newFiles
        );

        $id = $response->json('id');
        \Storage::assertMissing("$id/{$files['thumb_file']->hashName()}");
        \Storage::assertMissing("$id/{$files['video_file']->hashName()}");
    }
    protected function getFiles()
    {
        return [
            'video_file' => UploadedFile::fake()->create('video_file.mp4'),
            'thumb_file' => UploadedFile::fake()->create('image.jpg'),
            'banner_file' => UploadedFile::fake()->create('banner.jpg'),
            'trailer_file' => UploadedFile::fake()->create('trailer.mp4'),
        ];
    }

    protected function assertFilesOnPersist(TestResponse $response, $files)
    {
        $id = $response->json('id');
        $video = Video::find($id);
        $this->assertFilesExistsInStorage($video, $files);
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function routeDestroy()
    {
        return route('videos.destroy', ['video' => $this->video->id]);
    }

    protected function model()
    {
        return Video::class;
    }
}