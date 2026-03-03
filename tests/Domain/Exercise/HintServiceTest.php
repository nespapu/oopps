<?php

declare(strict_types=1);

namespace Tests\Domain\Exercise;

use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\HintService;
use PHPUnit\Framework\TestCase;

final class HintServiceTest extends TestCase
{
    /**
     * @dataProvider lettersModeExamplesProvider
     */
    public function testGetHint_lettersMode(string $value, Difficulty $difficulty, string $expected): void
    {
        $service = new HintService();

        $actual = $service->getHint($value, $difficulty, HintMode::LETTERS);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider wordsModeExamplesProvider
     */
    public function testGetHint_wordsMode(string $value, Difficulty $difficulty, string $expected): void
    {
        $service = new HintService();

        $actual = $service->getHint($value, $difficulty, HintMode::WORDS);

        self::assertSame($expected, $actual);
    }

    public static function lettersModeExamplesProvider(): array
    {
        $value = 'Diseño de bases de datos relacionales';

        return [
            'VERY_EASY' => [$value, Difficulty::VERY_EASY, 'Diseñ_ de base_ de dato_ relacionale_'],
            'EASY' => [$value, Difficulty::EASY, 'Dis___ de bas__ de dat__ relaci______'],
            'MEDIUM' => [$value, Difficulty::MEDIUM, 'D____o de b___s de d___s r__________s'],
            'HARD' => [$value, Difficulty::HARD, 'D_____ de b____ de d____ r___________'],
            'VERY_EASY_link_words' => ['Sistemas de gestión de archivos y dispositivos', Difficulty::VERY_EASY, 'Sistema_ de gestió_ de archivo_ y dispositivo_'],
            'MEDIUM_punctuation' => ['Diseño de bases, de datos relacionales.', Difficulty::MEDIUM, 'D____o de b___s, de d___s r__________s.'],
        ];
    }

    /**
     * DOMAIN RULE:
     * Linking words (articles, prepositions, conjunctions) are not masked
     * and do not count as content words.
     *
     * Examples of linking words:
     * de, del, la, el, los, las, y, en, por, para, con, sin
     */
    public static function wordsModeExamplesProvider(): array
    {
        $value = 'Diseño de bases de datos relacionales';

        return [
            'VERY_EASY' => [$value, Difficulty::VERY_EASY, 'Diseño de bases de ____ relacionales'],
            'EASY' => [$value, Difficulty::EASY, 'Diseño de ____ de ____ relacionales'],
            'MEDIUM' => [$value, Difficulty::MEDIUM, 'Diseño de ____ de ____ relacionales'],
            'MEDIUM_variation' => ['Lenguajes para la definición y manipulación de datos', Difficulty::MEDIUM, 'Lenguajes para la ____ y ____ de datos'],
            'HARD' => [$value, Difficulty::HARD, 'Diseño de ____ de ____ ____'],
            'HARD_variation' => ['Lenguajes para la definición y manipulación de datos', Difficulty::HARD, 'Lenguajes para la ____ y ____ de ____'],
            'VERY_EASY_punctuation' => ['Diseño de bases, de datos relacionales.', Difficulty::VERY_EASY, 'Diseño de bases, de ____ relacionales.'],
            'VERY_EASY_punctuation_inside_masked_word' => ['Diseño de bases de datos, relacionales', Difficulty::VERY_EASY, 'Diseño de bases de ____, relacionales'],
        ];
    }
}
