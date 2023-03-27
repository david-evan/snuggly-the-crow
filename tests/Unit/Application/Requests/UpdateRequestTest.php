<?php

namespace Tests\Unit\Application\Requests;

use App\Modules\Blog\Requests\UpdateArticleRequest;
use Domain\Blog\Entities\Article;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UpdateRequestTest extends TestCase
{
    #[DataProvider('provide_invalidUpdateArticleRequestBody')]
    public function test_updateRequestValidationRules(array $data): void
    {
        $request = new UpdateArticleRequest();
        $this->expectException(ValidationException::class);
        Validator::validate($data, $request->rules());
    }

    /* ---------- DATA PROVIDERS ----------  */

    public static function provide_invalidUpdateArticleRequestBody() : \Iterator
    {
        yield 'No data provided' => [
            []
        ];
        yield 'Missing status' => [
            ['title' => fake()->sentence(), 'content' => fake()->sentence()]
        ];
        yield 'Invalid status' => [
            ['title' => fake()->sentence(), 'content' => fake()->sentence(), 'status' => Str::random()]
        ];
        yield 'Title too long' => [
            ['title' => Str::random(Article::TITLE_MAX_LENGTH + 1), 'content' => fake()->sentence(), 'status' => 'draft']
        ];
        yield 'Title too short' => [
            ['title' => Str::random(Article::TITLE_MIN_LENGTH - 1), 'content' => fake()->sentence(), 'status' => 'draft']
        ];
        yield 'Invalid date format' => [
            ['title' => fake()->sentence(), 'content' => fake()->sentence(), 'status' => 'draft', 'published_at' => now()->format('d-m-Y-H-i-s')]
        ];
    }
}
