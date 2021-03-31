<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Traits;

use Tests\Stubs\Models\UploadFileStub;
use Tests\TestCase;

class UploadFilesTest extends TestCase
{
    /** @var UploadFileStub */
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFileStub();
        UploadFileStub::dropTable();
        UploadFileStub::createTable();
    }

    public function testMakeOldFieldsOnSaving()
    {

        $this->obj->fill([
            'name'  => 'test',
            'file1' => 'test1.mp4',
            'file2' => 'test2.mp4',
        ]);
        $this->obj->save();

        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update([
            'name'  => 'test_name',
            'file2' => 'test3.mp4'
        ]);
        $this->assertEqualsCanonicalizing(['test2.mp4'], $this->obj->oldFiles);


        $this->obj->fill([
            'name'  => 'test',
            'file1' => 'test1.mp4',
            'file2' => 'test2.mp4',
        ]);
        $this->obj->save();
    }

    public function testMakeOldFilesNullOnSave()
    {
        $this->obj->fill([
            'name' => 'test'
        ]);
        $this->obj->save();

        $this->assertEqualsCanonicalizing([], $this->obj->oldFiles);
    }
}