<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{
    protected function assertStore(
        array $sendData,
        array $testDatabase,
        array $testJsonData = null
    ): TestResponse {
        /**@var TestResponse $response */
        $response = $this->json('POST', $this->routeStore(), $sendData);
        $this->assertStatusCode($response, 201);
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContent($response, $testDatabase, $testJsonData);

        return $response;
    }

    protected function assertUpdate(
        array $sendData,
        array $testDatabase,
        array $testJsonData = null
    ): TestResponse {
        /**@var TestResponse $response */
        $response = $this->json('PUT', $this->routeUpdate(), $sendData);
        $this->assertStatusCode($response, 200);
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContent($response, $testDatabase, $testJsonData);

        return $response;
    }

    private function assertStatusCode(TestResponse $response, int $statusCode): void
    {
        if ($response->status() !== $statusCode)
            throw new \Exception("Response status must be ${$statusCode}, give {$response->status()}:\n{$response->content()}");
    }

    private function assertInDatabase(TestResponse $response, array $testDatabase)
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json('id')]);
    }

    private function assertJsonResponseContent(TestResponse $response, array $testDatabase, array $testJsonData = null)
    {
        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
    }
}
