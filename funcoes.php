<?php
function tresOitavosSimpson($fx, $passo)
{
    $resultado = 0;
    $tamanho = count($fx);
    for ($i = 0; $i < $tamanho; $i++) {
        if (($i == 0) || ($i == ($tamanho - 1))) {
            $resultado = $resultado + $fx[$i];
        } else {
            if (($i % 2) == 0) {
                $resultado = $resultado + (2 * $fx[$i]);
            } else {
                $resultado = $resultado + (4 * $fx[$i]);
            }
        }

    }
    $resultado = ($passo / 3) * $resultado;

    return $resultado;
}

function erroTresOitavosSimpson($derivada, $passo)
{
    $resultado = 0;
    $final = str_replace("X", "1", $derivada);
    $ExpDerivada = eval('return ' . $final . ';');

    $resultado = (-((3 / 80) * pow($passo, 5)) * ($ExpDerivada));

    return $resultado;
}

function erroTercoSimpson($derivada, $passo, $valorX)
{
    // Assim melhorou o resultado, mas ainda ta bem longe. Provavelmente tem que derivar 4 vezes essa variavel final, mas nao procurei muito bem como.
    $resultado = 0;
    $final = str_replace("X", $valorX, $derivada);
    $ExpDerivada = eval('return ' . $final . ';');

    $resultado = (-((1 / 90) * pow($passo, 5)) * ($ExpDerivada));

    return $resultado;
}

function calculaH($limSuperior, $limInferior, $nIntervalos)
{
    $h = ($limSuperior - $limInferior) / $nIntervalos;
    return $h;
}

function tabelaY($limEsquerdo, $limDireito, $passo, $repeticoes, $integral)
{
    $x = 0;
    $matriz = [];
    $string = "";
    for ($i = 0; $i < ($repeticoes + 1); $i++) {
        for ($j = 0; $j < 3; $j++) {
            if ($j == 0) {
                $matriz[$i][$j] = $i;
            } else {
                if ($j == 1) {
                    if ($i == 0) {
                        $matriz[$i][$j] = $limEsquerdo;
                        $x = $matriz[$i][$j];
                    } else {
                        $matriz[$i][$j] = $matriz[$i - 1][$j] + $passo;
                        $x = $matriz[$i][$j];
                    }
                } else {
                    $string = str_replace("X", $x, $integral);
                    $matriz[$i][$j] = eval('return ' . $string . ';');
                }
            }
        }
    }
    return $matriz;
}

function valorY($matriz)
{
    $y = [];
    for ($i = 0; $i < count($matriz); $i++) {
        for ($j = 0; $j < count($matriz[$i]); $j++) {
            if ($j == 2) {
                array_push($y, $matriz[$i][$j]);
            }
        }
    }
    return $y;
}

function tercoDeSimpson($y, $passo)
{
    $resultado = 0;
    for ($i = 0; $i < count($y); $i++) {
        if ($i == 0 || $i == (count($y) - 1)) {
            $resultado += $y[$i];
        } else {
            if (($i % 2) == 0) {
                $resultado += (2 * $y[$i]);
            } else {
                $resultado += (4 * $y[$i]);
            }
        }
    }
    $resultado = bcmul((bcdiv($passo, 3)), $resultado);
    return $resultado;
}

function trapezio(array $y, float $h)
{
    $result = (double) 0;

    foreach ($y as $key => $value) {
        if ($key == 0 || $key == (count($y) - 1)) {
            $result = bcadd($result, $value);
        } else {
            $result = bcadd($result, bcmul(2, $value));
        }
    }

    $result = bcmul(bcdiv($h, 2), $result);
    return $result;
}

function erroTrapezio(float $h, float $limSup, float $limInf, string $derivada)
{
    //TODO passar o resultado de x pra dar o replace no valor correto
    $result = (double) 0;
    $retFinal = (string) str_replace("X", "3", $derivada);
    $devF = (double) eval('return ' . $retFinal . ';');
    $result = (-((($limSup - $limInf) * bcpow($h, "3")) / 12)) * $devF;

    return $result;
}

function extrapolacaoRicharlison($identificador, $n1, $i1, $n2, $i2)
{
    $expoente = (int) 0;
    if ($identificador == 1) {
        $expoente = 2;
    } else {
        $expoente = 4;
    }
    $p1 = (bcpow($n1, $expoente)) / (bcsub(bcpow($n2, $expoente), bcpow($n1, $expoente)));
    $p2 = bcsub($i2, $i1);
    $resultado = bcadd($i2, (bcmul($p1, $p2)));
    return $resultado;
}

function quadraturaGaussiana($limEsquerdo, $limDireito, $numero, $integral)
{
    $retorno = "";
    if ($limEsquerdo == "-1" && $limDireito == "1") {
        $integral = str_replace("dx", "", $integral);
        $A = tabelaA($numero);
        $X = tabelaX($numero);

        for ($i = 0; $i < $numero; $i++) {
            if ($i == ($numero - 1)) {
                $retorno .= "bcmul(" . $A[$i] . " , " . str_replace("X", $X[$i], $integral) . ")";
            } else {
                $retorno .= "bcmul(" . $A[$i] . " , " . str_replace("X", $X[$i], $integral) . ")" . "+";
            }
        }

    } else {
        $integral = str_replace("dx", "", $integral);
        $A = tabelaA($numero);
        $X = tabelaX($numero);

        $dt = "(((" . $limDireito . ") - (" . $limEsquerdo . ")) / 2) * ";
        $t = "(((" . $limDireito . " - " . $limEsquerdo . ") / 2 ) * > + ((" . $limDireito . " + " . $limEsquerdo . ") / 2 ))";

        $integral = str_replace("X", $t, $integral);
        $integral = $dt . $integral;
        for ($i = 0; $i < $numero; $i++) {
            if ($i == ($numero - 1)) {
                $retorno .= $A[$i] . " * " . str_replace(">", "(" . $X[$i] . ")", $integral);
            } else {
                $retorno .= $A[$i] . " * " . str_replace(">", "(" . $X[$i] . ")", $integral) . "+";
            }
        }
    }

    return eval('return ' . $retorno . ';');
}

function tabelaA($n)
{
    $retorno = [];
    if ($n == 2) {
        array_push($retorno, 1);
        array_push($retorno, 1);
    } else if ($n == 3) {
        array_push($retorno, 0.5555555556);
        array_push($retorno, 0.8888888889);
        array_push($retorno, 0.5555555556);
    } else if ($n == 4) {
        array_push($retorno, 0.3478548451);
        array_push($retorno, 0.6521451549);
        array_push($retorno, 0.6521451549);
        array_push($retorno, 0.3478548451);
    } else if ($n == 5) {
        array_push($retorno, 0.2369268850);
        array_push($retorno, 0.4786286705);
        array_push($retorno, 0.5688888889);
        array_push($retorno, 0.4786286705);
        array_push($retorno, 0.2369268850);
    } else if ($n == 6) {
        array_push($retorno, 0.1713244924);
        array_push($retorno, 0.3607615730);
        array_push($retorno, 0.4679139346);
        array_push($retorno, 0.4679139346);
        array_push($retorno, 0.3607615730);
        array_push($retorno, 0.1713244924);
    } else if ($n == 7) {
        array_push($retorno, 0.1294849662);
        array_push($retorno, 0.2797053915);
        array_push($retorno, 0.3818300505);
        array_push($retorno, 0.4179591837);
        array_push($retorno, 0.3818300505);
        array_push($retorno, 0.2797053915);
        array_push($retorno, 0.1294849662);
    } else if ($n == 8) {
        array_push($retorno, 0.1012285363);
        array_push($retorno, 0.2223810345);
        array_push($retorno, 0.3137066459);
        array_push($retorno, 0.3626837838);
        array_push($retorno, 0.3626837838);
        array_push($retorno, 0.3137066459);
        array_push($retorno, 0.2223810345);
        array_push($retorno, 0.1012285363);
    }
    return $retorno;
}

function tabelaX($n)
{
    $retorno = [];
    if ($n == 2) {
        array_push($retorno, -0.5773502692);
        array_push($retorno, 0.5773502692);
    } else if ($n == 3) {
        array_push($retorno, -0.7745966692);
        array_push($retorno, 0);
        array_push($retorno, 0.7745966692);
    } else if ($n == 4) {
        array_push($retorno, -0.8611363116);
        array_push($retorno, -0.3399810436);
        array_push($retorno, 0.3399810436);
        array_push($retorno, 0.8611363116);
    } else if ($n == 5) {
        array_push($retorno, -0.9061798459);
        array_push($retorno, -0.5384693101);
        array_push($retorno, 0);
        array_push($retorno, 0.5384693101);
        array_push($retorno, 0.9061798459);
    } else if ($n == 6) {
        array_push($retorno, -0.9324695142);
        array_push($retorno, -0.6612093865);
        array_push($retorno, -0.2386191861);
        array_push($retorno, 0.2386191861);
        array_push($retorno, 0.6612093865);
        array_push($retorno, 0.9324695142);
    } else if ($n == 7) {
        array_push($retorno, -0.9491079123);
        array_push($retorno, -0.7415311855);
        array_push($retorno, -0.4058451513);
        array_push($retorno, 0);
        array_push($retorno, 0.4058451513);
        array_push($retorno, 0.7415311855);
        array_push($retorno, 0.9491079123);
    } else if ($n == 8) {
        array_push($retorno, -0.9602898565);
        array_push($retorno, -0.7966664774);
        array_push($retorno, -0.5255324099);
        array_push($retorno, -0.1834346425);
        array_push($retorno, 0.1834346425);
        array_push($retorno, 0.5255324099);
        array_push($retorno, 0.7966664774);
        array_push($retorno, 0.9602898565);
    }
    return $retorno;
}
