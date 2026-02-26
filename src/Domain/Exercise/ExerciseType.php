<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

final class ExerciseType
{
    private string $slug;
    private string $name;

    private function __construct(string $slug, string $name)
    {
        $slug = trim($slug);
        $name = trim($name);

        if ($slug === '') {
            throw new \InvalidArgumentException('ExerciseType slug cannot be empty.');
        }
        if ($name === '') {
            throw new \InvalidArgumentException('ExerciseType name cannot be empty.');
        }

        $this->slug = $slug;
        $this->name = $name;
    }

    public static function howMuchDoYouKnowTopic(): self
    {
        return new self('cuanto-sabes-tema', 'Cuánto sabes del tema');
    }

    public static function theoreticalExamSimulation(): self
    {
        return new self('simulacro-examen-teorico', 'Simulacro examen teórico');
    }

    /**
     * @return array<string,self>
     */
    public static function all(): array
    {
        $types = [
            self::howMuchDoYouKnowTopic(),
            self::theoreticalExamSimulation(),
        ];

        $bySlug = [];
        foreach ($types as $type) {
            $bySlug[$type->slug()] = $type;
        }

        return $bySlug;
    }

    public static function fromSlug(string $slug): self
    {
        $slug = trim($slug);
        $all = self::all();

        if (!isset($all[$slug])) {
            throw new \InvalidArgumentException("Unknown ExerciseType slug: {$slug}");
        }

        return $all[$slug];
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function equals(self $other): bool
    {
        return $this->slug === $other->slug;
    }
}