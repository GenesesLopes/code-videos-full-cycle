<?php

use App\Models\{
    Video,
    Genre
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class VideoSeeder extends Seeder
{
    /** @var Genre */
    private $allGenres;

    private $relations = [
        'genres_id' => [],
        'categories_id' => []
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dir = \Storage::getDriver()->getAdapter()->getPathPrefix();
        \File::deleteDirectory($dir, true);

        $self = $this;
        $this->allGenres = Genre::all();
        Model::reguard(); //mass assigment
        factory(Video::class, 100)
            ->make()
            ->each(function (Video $video) use ($self) {
                $self->fetchRelations();
                Video::create(
                    array_merge(
                        $video->toArray(),
                        [
                            'thumb_file' => $self->getImageFile(),
                            'banner_file' => $self->getImageFile(),
                            'trailer_file' => $self->getVideoFile(),
                            'video_file' => $self->getVideoFile(),
                        ],
                        $this->relations
                    )
                );
                // $subGenres = $genres->random(5)->load('categories');
                // $categoriesId = [];
                // foreach ($subGenres as $genre) {
                //     array_push($categoriesId, ...$genre->categories->pluck('id')->toArray());
                // }
                // $categoriesId = array_unique($categoriesId);
                // $video->categories()->attach($categoriesId);
                // $video->genres()->attach($subGenres->pluck('id')->toArray());
            });
        Model::unguard();
    }

    public function fetchRelations()
    {
        $subGenres = $this->allGenres->random(5)->load('categories');
        $categoriesId = [];
        foreach ($subGenres as $genre) {
            array_push($categoriesId, ...$genre->categories->pluck('id')->toArray());
        }
        $categoriesId = array_unique($categoriesId);
        $genresId = $subGenres->pluck('id')->toArray();
        $this->relations['categories_id'] = $categoriesId;
        $this->relations['genres_id'] = $genresId;
    }

    public function getImageFile()
    {
        return new UploadedFile(
            storage_path('app/fake/thumbs/LaravelFramework.png'),
            'LaravelFramework.png'
        );
    }

    public function getVideoFile()
    {
        return new UploadedFile(
            storage_path('app/fake/videos/video.mp4'),
            'video.mp4'
        );
    }
}