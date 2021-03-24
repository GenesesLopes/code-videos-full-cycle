<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UploadFiles;

class UploadFileStub extends Model
{

    use UploadFiles;

    public static $fileFields = ['file1', 'file2'];

    protected function uploadDir()
    {
        return "1";
    }
    // protected $table = 'category_stubs';
    // protected $fillable = [
    //     'name',
    //     'description'
    // ];

    // public static function createTable()
    // {
    //     \Schema::create('category_stubs', function (Blueprint $table) {
    //         $table->bigIncrements('id');
    //         $table->string('name');
    //         $table->text('description')->nullable();
    //         $table->timestamps();
    //     });
    // }

    // public static function dropTable()
    // {
    //     \Schema::dropIfExists('category_stubs');
    // }
}