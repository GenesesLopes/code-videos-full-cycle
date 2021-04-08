<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
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
        $categories = factory(Category::class)->create();
        $genres = factory(Genre::class)->create();
        $genres->categories()->sync($categories->id);

        $response = $this->json(
            'POST',
            $this->routeStore(),
            $this->sendData +
                [
                    'categories_id' => [$categories->id],
                    'genres_id' => [$genres->id]
                ] +
                $files
        );
        $response->assertStatus(201);
        $id = $response->json('id');
        foreach ($files as $file) {
            \Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $categories = factory(Category::class)->create();
        $genres = factory(Genre::class)->create();
        $genres->categories()->sync($categories->id);

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendData +
                [
                    'categories_id' => [$categories->id],
                    'genres_id' => [$genres->id]
                ] +
                $files
        );
        $response->assertStatus(200);
        $id = $response->json('id');
        foreach ($files as $file) {
            \Storage::assertExists("$id/{$file->hashName()}");
        }
    }
    protected function getFiles()
    {
        return [
            'video_file' => UploadedFile::fake()->create('video_file.mp4'),
            'thumb_file' => UploadedFile::fake()->create('image.jpg')
        ];
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