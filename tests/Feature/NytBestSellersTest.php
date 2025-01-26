<?php

namespace Tests\Feature;

use App\Http\Requests\NytBestSellersRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NytBestSellersTest extends TestCase
{
    public function test_valid_request_data()
    {
        $request = new NytBestSellersRequest();

        $validator = Validator::make([
            'author' => 'Stephen King',
            'isbn' => ['1476727651', '9781476727653'],
            'title' => 'The Shining',
            'offset' => 10,
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_invalid_isbn()
    {
        $request = new NytBestSellersRequest();

        $validator = Validator::make([
            'isbn' => ['97812345678971234'], // Invalid (too long)
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('isbn.0', $validator->errors()->toArray());
    }

    public function test_best_sellers_endpoint_returns_data()
    {
        // Mock the NYT API response
        Http::fake([
            '*' => Http::response([
                'status' => 'OK',
                'results' => [
                    ['title' => 'Book 1', 'author' => 'Author 1'],
                    ['title' => 'Book 2', 'author' => 'Author 2'],
                ],
            ], 200),
        ]);

        // Call the endpoint
        $response = $this->getJson('/api/v1/nyt/best-sellers?author=Author+1');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'OK',
                'results' => [
                    ['title' => 'Book 1', 'author' => 'Author 1'],
                ],
            ]);
    }

    public function test_best_sellers_endpoint_handles_invalid_response()
    {
        // Mock a failed NYT API response
        Http::fake([
            '*' => Http::response(['fault' => 'Invalid API Key'], 401),
        ]);

        // Call the endpoint
        $response = $this->getJson('/api/v1/nyt/best-sellers');

        // Assert the response
        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Failed to fetch data from NYT API: {"fault":"Invalid API Key"}',
            ]);
    }
}
