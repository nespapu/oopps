<?php
declare(strict_types=1);

namespace Tests\Domain\Exercise;

use PHPUnit\Framework\TestCase;
use App\Domain\Exercise\PistaService;
use App\Domain\Exercise\ModoPista;
use App\Domain\Exercise\Dificultad;

final class PistaServiceTest extends TestCase
{
    /**
     * @dataProvider ejemplosLetrasProvider
     */
    public function testGetPista_modoLetras(string $valor, Dificultad $dificultad, string $esperado): void
    {
        $servicio = new PistaService();

        $actual = $servicio->getPista($valor, $dificultad, ModoPista::LETRAS);

        self::assertSame($esperado, $actual);
    }

    /**
     * @dataProvider ejemplosPalabrasProvider
     */
    public function testGetPista_wordsMode(string $valor, Dificultad $dificultad, string $esperado): void
    {
        $servicio = new PistaService();

        $actual = $servicio->getPista($valor, $dificultad, ModoPista::PALABRAS);

        self::assertSame($esperado, $actual);
    }

    public static function ejemplosLetrasProvider(): array
    {
        $valor = 'Diseño de bases de datos relacionales';

        return [
            'MUY_FACIL' => [$valor, Dificultad::MUY_FACIL, 'Diseñ_ de base_ de dato_ relacionale_'],
            'FACIL'     => [$valor, Dificultad::FACIL,     'Dis___ de bas__ de dat__ relaci______'],
            'MEDIA'     => [$valor, Dificultad::MEDIA,     'D____o de b___s de d___s r__________s'],
            'DIFICIL'   => [$valor, Dificultad::DIFICIL,   'D_____ de b____ de d____ r___________'],
            'MUY_FACIL_link_words' => ['Sistemas de gestión de archivos y dispositivos', Dificultad::MUY_FACIL, 'Sistema_ de gestió_ de archivo_ y dispositivo_'],
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
            'MUY_FACIL' => [$valor, Dificultad::MUY_FACIL, 'Diseño de bases de ____ relacionales'],
            'FACIL'     => [$valor, Dificultad::FACIL,     'Diseño de ____ de ____ relacionales'],
            'MEDIA'     => [$valor, Dificultad::MEDIA,     'Diseño de ____ de ____ relacionales'],
            'MEDIA_distinto' => ['Lenguajes para la definición y manipulación de datos', Dificultad::MEDIA, 'Lenguajes para la ____ y ____ de datos'],
            'DIFICIL'   => [$valor, Dificultad::DIFICIL,   'Diseño de ____ de ____ ____'],
            'DIFICIL_distinct' => ['Lenguajes para la definición y manipulación de datos', Dificultad::DIFICIL, 'Lenguajes para la ____ y ____ de ____']
        ];
    }
}
