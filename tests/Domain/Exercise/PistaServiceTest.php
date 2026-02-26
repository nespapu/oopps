<?php
declare(strict_types=1);

namespace Tests\Domain\Exercise;

use PHPUnit\Framework\TestCase;
use App\Domain\Exercise\HintService;
use App\Domain\Exercise\HintMode;
use App\Domain\Exercise\Difficulty;

final class PistaServiceTest extends TestCase
{
    /**
     * @dataProvider ejemplosLetrasProvider
     */
    public function testGetPista_modoLetras(string $valor, Difficulty $dificultad, string $esperado): void
    {
        $servicio = new HintService();

        $actual = $servicio->getHint($valor, $dificultad, HintMode::LETTERS);

        self::assertSame($esperado, $actual);
    }

    /**
     * @dataProvider ejemplosPalabrasProvider
     */
    public function testGetPista_wordsMode(string $valor, Difficulty $dificultad, string $esperado): void
    {
        $servicio = new HintService();

        $actual = $servicio->getHint($valor, $dificultad, HintMode::WORDS);

        self::assertSame($esperado, $actual);
    }

    public static function ejemplosLetrasProvider(): array
    {
        $valor = 'Diseño de bases de datos relacionales';

        return [
            'MUY_FACIL' => [$valor, Difficulty::VERY_EASY, 'Diseñ_ de base_ de dato_ relacionale_'],
            'FACIL'     => [$valor, Difficulty::EASY,     'Dis___ de bas__ de dat__ relaci______'],
            'MEDIA'     => [$valor, Difficulty::MEDIUM,     'D____o de b___s de d___s r__________s'],
            'DIFICIL'   => [$valor, Difficulty::HARD,   'D_____ de b____ de d____ r___________'],
            'MUY_FACIL_link_words' => ['Sistemas de gestión de archivos y dispositivos', Difficulty::VERY_EASY, 'Sistema_ de gestió_ de archivo_ y dispositivo_'],
            'MEDIA_puntuacion' => ['Diseño de bases, de datos relacionales.', Difficulty::MEDIUM, 'D____o de b___s, de d___s r__________s.'],
        ];
    }

    /**
     * REGLA DE DOMINIO:
     * Las palabras de enlace (artículos, preposiciones, conjunciones)
     * no se enmascaran ni cuentan como palabras de contenido.
     *
     * Ejemplos de palabras de enlace:
     * de, del, la, el, los, las, y, en, por, para, con, sin
     */
    public static function ejemplosPalabrasProvider(): array
    {
        $valor = 'Diseño de bases de datos relacionales';

        return [
            'MUY_FACIL' => [$valor, Difficulty::VERY_EASY, 'Diseño de bases de ____ relacionales'],
            'FACIL'     => [$valor, Difficulty::EASY,     'Diseño de ____ de ____ relacionales'],
            'MEDIA'     => [$valor, Difficulty::MEDIUM,     'Diseño de ____ de ____ relacionales'],
            'MEDIA_distinto' => ['Lenguajes para la definición y manipulación de datos', Difficulty::MEDIUM, 'Lenguajes para la ____ y ____ de datos'],
            'DIFICIL'   => [$valor, Difficulty::HARD,   'Diseño de ____ de ____ ____'],
            'DIFICIL_distinct' => ['Lenguajes para la definición y manipulación de datos', Difficulty::HARD, 'Lenguajes para la ____ y ____ de ____'],
            'MUY_FACIL_puntuacion' => ['Diseño de bases, de datos relacionales.', Difficulty::VERY_EASY, 'Diseño de bases, de ____ relacionales.'],
            'MUY_FACIL_puntuacion_en_palabra_enmascarada' => ['Diseño de bases de datos, relacionales', Difficulty::VERY_EASY, 'Diseño de bases de ____, relacionales'],
        ];
    }
}
