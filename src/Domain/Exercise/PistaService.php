<?php
declare(strict_types=1);

namespace App\Domain\Exercise;

final class PistaService
{
    private const CHARSET = 'UTF-8';
    private const MASCARA_PALABRA = '____';
    private const MASCARA_LETRA = '_';

    /**
     * Regla de dominio: Las palabras de enlace (artículos, preposiciones, conjunciones)
     * nunca son enmascaradas ni se tienen en cuenta como palabras con contenido semántico significativo.
     */
    private const PALABRAS_DE_ENLACE = [
        'a', 'al', 'con', 'de', 'del', 'el', 'en',
        'la', 'las', 'los', 'para', 'por', 'sin', 'y',
    ];

    public function getPista(
        string $valor,
        Difficulty $dificultad,
        HintMode $modo
    ): string {
        if (trim($valor) === '') {
            return '';
        }

        $valor = $this->normalizarEspacios($valor);

        $palabras = explode(' ', $valor);
        $indicesContenido = $this->getIndicesPalabrasContenidoSemantico($palabras);

        return match ($modo) {
            HintMode::WORDS => $this->generarPistaPorPalabras($palabras, $indicesContenido, $dificultad, $valor),
            HintMode::LETTERS   => $this->generarPistaPorLetras($palabras, $indicesContenido, $dificultad),
        };
    }

    private function normalizarEspacios(string $valor): string
    {
        $valor = trim($valor);
        return preg_replace('/\s+/', ' ', $valor) ?? $valor;
    }

    /**
     * @param string[] $palabras
     * @param int[] $indicesContenido
     */
    private function generarPistaPorPalabras(
        array $palabras,
        array $indicesContenido,
        Difficulty $dificultad,
        string $valorOriginal
    ): string {
        $total = count($indicesContenido);

        return match ($dificultad) {
            Difficulty::VERY_EASY => $this->palabrasMuyFacil($palabras, $indicesContenido, $total, $valorOriginal),
            Difficulty::EASY     => $this->palabrasFacil($palabras, $indicesContenido, $total, $valorOriginal),
            Difficulty::MEDIUM     => $this->palabrasMedia($palabras, $indicesContenido, $total, $valorOriginal),
            Difficulty::HARD   => $this->palabrasDificil($palabras, $indicesContenido, $total, $valorOriginal),
        };
    }

    /**
     * @param string[] $palabras
     * @param int[] $indicesContenido
     */
    private function palabrasMuyFacil(array $palabras, array $indicesContenido, int $total, string $valorOriginal): string
    {
        if ($total === 0) {
            return $valorOriginal;
        }

        $posicionMedia = intdiv($total, 2);
        $indice = $indicesContenido[$posicionMedia];

        $palabras[$indice] = $this->enmascararNucleoParaPalabras($palabras[$indice]);

        return implode(' ', $palabras);
    }

    /**
     * @param string[] $palabras
     * @param int[] $indicesContenido
     */
    private function palabrasFacil(array $palabras, array $indicesContenido, int $total, string $valorOriginal): string
    {
        if ($total < 2) {
            return $valorOriginal;
        }

        $posicionIzq = intdiv($total - 1, 2);
        $posicionDcha = $posicionIzq + 1;

        $indiceIzq = $indicesContenido[$posicionIzq];
        $indiceDcha = $indicesContenido[$posicionDcha];

        $palabras[$indiceIzq] = $this->enmascararNucleoParaPalabras($palabras[$indiceIzq]);
        $palabras[$indiceDcha] = $this->enmascararNucleoParaPalabras($palabras[$indiceDcha]);

        return implode(' ', $palabras);
    }

    /**
     * @param string[] $palabras
     * @param int[] $indicesContenido
     */
    private function palabrasMedia(array $palabras, array $indicesContenido, int $total, string $valorOriginal): string
    {
        if ($total <= 2) {
            return $valorOriginal;
        }

        $indicePrimera = $indicesContenido[0];
        $indiceUltima  = $indicesContenido[$total - 1];

        foreach ($indicesContenido as $indice) {
            if ($indice === $indicePrimera || $indice === $indiceUltima) {
                continue;
            }
            $palabras[$indice] = $this->enmascararNucleoParaPalabras($palabras[$indice]);
        }

        return implode(' ', $palabras);
    }

    /**
     * @param string[] $palabras
     * @param int[] $indicesContenido
     */
    private function palabrasDificil(array $palabras, array $indicesContenido, int $total, string $valorOriginal): string
    {
        if ($total <= 1) {
            return $valorOriginal;
        }

        $indicePrimera = $indicesContenido[0];

        foreach ($indicesContenido as $indice) {
            if ($indice === $indicePrimera) {
                continue;
            }
            $palabras[$indice] = $this->enmascararNucleoParaPalabras($palabras[$indice]);
        }

        return implode(' ', $palabras);
    }

    /**
     * @param string[] $palabras
     * @param int[] $indicesContenido
     */
    private function generarPistaPorLetras(array $palabras, array $indicesContenido, Difficulty $dificultad): string
    {
        $enmascarador = match ($dificultad) {
            Difficulty::VERY_EASY => fn(string $p): string => $this->enmascararUltimaLetra($p),
            Difficulty::EASY     => fn(string $p): string => $this->enmascararLetrasSegundaMitad($p),
            Difficulty::MEDIUM     => fn(string $p): string => $this->enmascararPrimeraUltimaLetra($p),
            Difficulty::HARD   => fn(string $p): string => $this->enmascararTodasLetrasSalvoPrimera($p),
        };

        foreach ($indicesContenido as $indice) {
            $palabras[$indice] = $this->enmascararNucleoParaLetras($palabras[$indice], $enmascarador);
        }

        return implode(' ', $palabras);
    }

    /**
     * Devuelve el índice de las palabras consideradas como semánticamente significativas,
     * excluyendo las palabras de enlace de ser enmascaradas o contadas.
     *
     * @param string[] $palabras
     * @return int[]
     */
    private function getIndicesPalabrasContenidoSemantico(array $palabras): array
    {
        $indices = [];

        foreach ($palabras as $i => $palabra) {
            if ($palabra === '') {
                continue;
            }

            if ($this->esPalabraEnlace($palabra)) {
                continue;
            }

            $indices[] = $i;
        }

        return $indices;
    }

    private function esPalabraEnlace(string $palabra): bool
    {
        $normalizada = $this->normalizarPalabraParaComparacion($palabra);

        if ($normalizada === '') {
            return false;
        }

        return in_array($normalizada, self::PALABRAS_DE_ENLACE, true);
    }

    /**
     * Normaliza una palabra para ser comparada con las palabras de enlace:
     * - Convierte a minúsculas
     * - Quita símbolos de puntuación iniciales o finales, mantiene los de en medio
     * - Conserva los acentos
     */
    private function normalizarPalabraParaComparacion(string $palabra): string
    {
        $palabra = mb_strtolower($palabra, self::CHARSET);
        $palabra = preg_replace('/^[^\p{L}\p{N}]+|[^\p{L}\p{N}]+$/u', '', $palabra);

        return $palabra ?? '';
    }

    private function enmascararUltimaLetra(string $palabra): string
    {
        $longitud = mb_strlen($palabra, self::CHARSET);

        if ($longitud <= 1) {
            return $palabra;
        }

        return mb_substr($palabra, 0, $longitud - 1, self::CHARSET) . self::MASCARA_LETRA;
    }

    private function enmascararLetrasSegundaMitad(string $palabra): string
    {
        $longitud = mb_strlen($palabra, self::CHARSET);

        if ($longitud <= 1) {
            return $palabra;
        }

        $contadorVisibles = intdiv($longitud + 1, 2);
        $contadorEnmascaradas = $longitud - $contadorVisibles;

        return mb_substr($palabra, 0, $contadorVisibles, self::CHARSET)
            . str_repeat(self::MASCARA_LETRA, $contadorEnmascaradas);
    }

    private function enmascararPrimeraUltimaLetra(string $palabra): string
    {
        $longitud = mb_strlen($palabra, self::CHARSET);

        if ($longitud <= 2) {
            return $palabra;
        }

        $primera = mb_substr($palabra, 0, 1, self::CHARSET);
        $ultima  = mb_substr($palabra, $longitud - 1, 1, self::CHARSET);

        return $primera . str_repeat(self::MASCARA_LETRA, $longitud - 2) . $ultima;
    }

    private function enmascararTodasLetrasSalvoPrimera(string $palabra): string
    {
        $longitud = mb_strlen($palabra, self::CHARSET);

        if ($longitud <= 1) {
            return $palabra;
        }

        $primera = mb_substr($palabra, 0, 1, self::CHARSET);

        return $primera . str_repeat(self::MASCARA_LETRA, $longitud - 1);
    }

    private function enmascararNucleoParaLetras(string $palabra, callable $enmascarador): string
    {
        [$prefijo, $nucleo, $sufijo] = $this->dividirPalabra($palabra);

        $nucleoEnmascarado = $enmascarador($nucleo);

        return $prefijo . $nucleoEnmascarado . $sufijo;
    }

    private function enmascararNucleoParaPalabras(string $palabra): string
    {
        [$prefijo, $nucleo, $sufijo] = $this->dividirPalabra($palabra);

        // Enmascar solo si hay un núcleo significativo
        if ($nucleo === '' || !preg_match('/[\p{L}\p{N}]/u', $nucleo)) {
            return $palabra;
        }

        return $prefijo . self::MASCARA_PALABRA . $sufijo;
    }

    private function dividirPalabra(string $palabra): array
    {
        // prefijo: caracter no alfanumérico al principio
        // núcleo:    caracteres alfanuméricos
        // sufijo: ncaracter no alfanumérico al final
        if (preg_match('/^([^\p{L}\p{N}]*)([\p{L}\p{N}]+)([^\p{L}\p{N}]*)$/u', $palabra, $m) !== 1) {
            return ['', $palabra, ''];
        }

        return [$m[1], $m[2], $m[3]];
    }

}
?>