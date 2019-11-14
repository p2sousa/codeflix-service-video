<?php


namespace Tests\Feature\Models\Traits;


use Tests\Stubs\Models\Traits\UploadFilesStub;
use Tests\TestCase;

class UploadFilesTest extends TestCase
{
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFilesStub();

        UploadFilesStub::dropTable();
        UploadFilesStub::makeTable();
    }



    public function testMakeOldFieldsOnSaving()
    {
        $this->obj->fill([
            'name' => 'teste',
            'file1' => 'video1.mp4',
            'file2' => 'video2.mp4',
        ]);

        $this->obj->save();
        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update([
            'name' => 'test_name',
            'file2' => 'video3.mp4',
        ]);

        $this->assertEqualsCanonicalizing(['video2.mp4'], $this->obj->oldFiles);
    }

    public function testMakeOldFilesNullOnSave()
    {
        $this->obj->fill([
            'name' => 'teste',
        ]);

        $this->obj->save();
        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update([
            'name' => 'test_name',
            'file2' => 'video3.mp4',
        ]);

        $this->assertEqualsCanonicalizing([], $this->obj->oldFiles);
    }
}
