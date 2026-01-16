<?php
namespace App\Domain\Exercise;

final class PistaService
{
    /**
     * Regla de dominio: Las palabras de enlace (articulos, preposiciones, conjunciones)
     * nunca son enmascaradas ni se tienen en cuenta como palabras con contenido semántico significativo.
     */
    private const PALABRAS_DE_ENLACE = [
        'a', 'al', 'con', 'de', 'del', 'el', 'en',
        'la', 'las', 'los', 'para', 'por', 'sin', 'y',
    ];

    public function getPista(
        string $valor,
        Dificultad $dificultad,
        ModoPista $modo
    ): string {
        if(trim($valor) === '')
        {
            return '';
        }

        $valor = preg_replace('/\s+/', ' ', trim($valor));

        $palabras = explode(' ', $valor);
        $indicesPalabrasContenidoSemantico = $this->getIndicesPalabrasContenidoSemantico($palabras);

        if ($modo === ModoPista::PALABRAS && $dificultad === Dificultad::MUY_FACIL) {
            if (count($indicesPalabrasContenidoSemantico) === 0) {
                return $valor;
            }

            // Opción determinista: palabra de contenido que se encuentra en el medio
            $posicionMedia = intdiv(count($indicesPalabrasContenidoSemantico), 2);
            $indicePalabraEnmascar = $indicesPalabrasContenidoSemantico[$posicionMedia];

            $palabras[$indicePalabraEnmascar] = '____';

            return implode(' ', $palabras);
        }

        if ($modo === ModoPista::PALABRAS && $dificultad === Dificultad::FACIL) {
            $contador = count($indicesPalabrasContenidoSemantico);

            if ($contador < 2) {
                return $valor; // No hay suficientes palabras con contenido semántico para enmascarar dos
            }

            // Opción determinista: elegimos las dos palabras de contenido semánticas más centradas
            // Para un número par (e.g., 4) -> elegimos las posiciones 1 y 2
            // Para un número impar (e.g., 5) -> elegimos las posiciones 2 y 3
            $posicionIzq = intdiv($contador - 1, 2);
            $posicionDcha = $posicionIzq + 1;

            $palabras[$indicesPalabrasContenidoSemantico[$posicionIzq]] = '____';
            $palabras[$indicesPalabrasContenidoSemantico[$posicionDcha]] = '____';

            return implode(' ', $palabras);
        }

        if ($modo === ModoPista::PALABRAS && $dificultad === Dificultad::MEDIA) {
            $contador = count($indicesPalabrasContenidoSemantico);

            if ($contador <= 2) {
                return $valor; // nada o casi nada para enmascarar
            }

            // Opción determinista: Mantener visibles la primera y última palabra
            $indicePrimeraPalabra = $indicesPalabrasContenidoSemantico[0];
            $indiceUltimaPalabra  = $indicesPalabrasContenidoSemantico[$contador - 1];

            foreach ($indicesPalabrasContenidoSemantico as $indice) {
                if ($indice === $indicePrimeraPalabra || $indice === $indiceUltimaPalabra) {
                    continue;
                }
                $palabras[$indice] = '____';
            }

            return implode(' ', $palabras);
        }

        if ($modo === ModoPista::PALABRAS && $dificultad === Dificultad::DIFICIL) {
            $contador = count($indicesPalabrasContenidoSemantico);

            if ($contador <= 1) {
                return $valor; // nada que enmascarar
            }

            // Opción determinista: Sólo dejar visible la primera palabra semántica significativa
            $indicePrimeraPalabra = $indicesPalabrasContenidoSemantico[0];

            foreach ($indicesPalabrasContenidoSemantico as $indice) {
                if ($indice === $indicePrimeraPalabra) {
                    continue;
                }
                $palabras[$indice] = '____';
            }

            return implode(' ', $palabras);
        }

        return $valor;
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
     * - Quita símbolos de puntuación inciales o finales, mantiene los de en medio
     * - Conserva los acentos
     */
    private function normalizarPalabraParaComparacion(string $palabra): string
    {
        $palabra = mb_strtolower($palabra, 'UTF-8');

        $palabra = preg_replace('/^[^\p{L}\p{N}]+|[^\p{L}\p{N}]+$/u', '', $palabra);

        return $palabra ?? '';
    }
}
