<?php

namespace Database\Factories;

use Domain\Blog\Entities\Article;
use Domain\Blog\ValueObjects\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'content' => fake()->sentence(),
        ];
    }

    public function draft(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Status::DRAFT->value,
            ];
        });
    }

    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Status::PUBLISHED->value,
                'published_at' => now()
            ];
        });
    }

    public function publishAtRandomDate(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => fake()->dateTimeThisMonth()
            ];
        });
    }
}
