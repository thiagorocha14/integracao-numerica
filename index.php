<?php

require_once('tresOitavosSimpson.php');
$string = (float) null;
$funcao = $_POST["funcao"];
$metodo = $_POST["metodo"];

//Limite esquerdo -> inferior
$limiteEsquerdo = $_POST["limiteEsquerdo"];

//Limite direito -> superior
$limiteDireito = $_POST["limiteDireito"];
$intervalo = $_POST["intervalo"];

if (isset($_POST['btnCalcular'])) {
    bcscale($_POST["precisao"]);
    if ($metodo == "1S") {
        $alturaFinal = bcdiv(($limiteDireito - $limiteEsquerdo), $intervalo);
        $matrizFinal = tabelaY($limiteEsquerdo, $limiteDireito, $alturaFinal, $intervalo, $funcao);
        $y = valorY($matrizFinal);
        $string = tercoDeSimpson($y, $alturaFinal);
    } else if ($metodo == "QG") {
        $string = quadraturaGaussiana($limiteEsquerdo, $limiteDireito, $intervalo, $funcao);
    } else if ($metodo == "RT") {
        $h = calculaH($limiteDireito, $limiteEsquerdo, $intervalo);
        $matrizFinal = tabelaY($limiteEsquerdo, $limiteDireito, $h, $intervalo, $funcao);
        $y = valorY($matrizFinal);
        $string = trapezio($y, $h);
    } else if ($metodo == "3S") {
        $h = calculaH($limiteDireito, $limiteEsquerdo, $intervalo);
        $matrizFinal = tabelaY($limiteEsquerdo, $limiteDireito, $h, $intervalo, $funcao);
        $y = valorY($matrizFinal);
        $string = tresOitavosSimpson($y, $h);
    }

}

function calculaH($limSuperior, $limInferior, $nIntervalos) {
    $h = ($limSuperior - $limInferior) / $nIntervalos;
    return $h;
}

function tabelaY($limEsquerdo, $limDireito, $altura, $repeticoes, $integral)
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
                        $matriz[$i][$j] = $matriz[$i - 1][$j] + $altura;
                        $x = $matriz[$i][$j];
                    }
                } else {
                    $string = str_replace("x", $x, $integral);
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

function tercoDeSimpson($y, $altura)
{
    $resultado = (double) 0;
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
    $resultado = ($altura / 3) * $resultado;
    return $resultado;
}

function trapezio(Array $y, float $h) {
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

function erroTrapezio(float $h, float $limSup, float $limInf, string $derivada) {
    $result = (double) 0;
    $retFinal = (string) $derivada.str_replace("x", "3");
    $devF = (double) eval('return ' . $retFinal . ';');
    $result = (-((($limSup - $limInf) * bcpow($h, "2")) / 12 )) * $devF;

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
    $p1 = bcdiv(bcpow($n1, $expoente), (bcsub(bcpow($n2, $expoente), bcpow($n1, $expoente))));
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
                $retorno .= "bcmul(" . $A[$i] . " , " . str_replace("x", $X[$i], $integral) . ")";
            } else {
                $retorno .= "bcmul(" . $A[$i] . " , " . str_replace("x", $X[$i], $integral) . ")" . "+";
            }
        }

    } else {
        $integral = str_replace("dx", "", $integral);
        $A = tabelaA($numero);
        $X = tabelaX($numero);

        $dt = "(((" . $limDireito . ") - (" . $limEsquerdo . ")) / 2) * ";
        $t = "(((" . $limDireito . " - " . $limEsquerdo . ") / 2 ) * l + ((" . $limDireito . " + " . $limEsquerdo . ") / 2 ))";

        $integral = str_replace("x", $t, $integral);
        $integral = $dt . $integral;
        for ($i = 0; $i < $numero; $i++) {
            if ($i == ($numero - 1)) {
                $retorno .= $A[$i] . " * " . str_replace("l", "(" . $X[$i] . ")", $integral);
            } else {
                $retorno .= $A[$i] . " * " . str_replace("l", "(" . $X[$i] . ")", $integral) . "+";
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

//Função para "traduzir" para bc, mas não está funcionando
function bc()
{
    $functions = 'sqrt';
    $argv = func_get_args();
    $string = str_replace(' ', '', '(' . $argv[0] . ')');
    $string = preg_replace('/\$([0-9\.]+)/', '$argv[$1]', $string);
    while (preg_match('/((' . $functions . ')?)\(([^\)\(]*)\)/', $string, $match)) {
        while (
            preg_match('/([0-9\.]+)(\^)([0-9\.]+)/', $match[3], $m) ||
            preg_match('/([0-9\.]+)([\*\/\%])([0-9\.]+)/', $match[3], $m) ||
            preg_match('/([0-9\.]+)([\+\-])([0-9\.]+)/', $match[3], $m)
        ) {
            switch ($m[2]) {
                case '+':$result = bcadd($m[1], $m[3]);
                    break;
                case '-':$result = bcsub($m[1], $m[3]);
                    break;
                case '*':$result = bcmul($m[1], $m[3]);
                    break;
                case '/':$result = bcdiv($m[1], $m[3]);
                    break;
                case '%':$result = bcmod($m[1], $m[3]);
                    break;
                case '^':$result = bcpow($m[1], $m[3]);
                    break;
            }
            $match[3] = str_replace($m[0], $result, $match[3]);
        }
        if (!empty($match[1]) && function_exists($func = 'bc' . $match[1])) {
            $match[3] = $func($match[3]);
        }
        $string = str_replace($match[0], $match[3], $string);
    }
    return $string;
}

?>
<html>

<head>
    <title>Integração Numérica</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <script>
    function updateTextInput(val) {
        document.getElementById('textInput').value = val;
    }
    </script>
    <br>
    <br>

    <form method="POST">
        <div class="container">
            <div class="row">
                <div class="col">
                    <select name="metodo">
                        <option value="1S">1/3 de Simpson</option>
                        <option value="3S">3/8 de Simpson</option>
                        <option value="ER">Extrapolação de Richarlison</option>
                        <option value="RT">Regra dos Trapézios</option>
                        <option value="QG">Quadratura Gauciana</option>
                    </select>
                </div>
                <div class="col">
                    <label>Função:&nbsp;</label><input type="text" name="funcao" placeholder="Função">
                </div>
                <div class="col">
                    <label>Precisão:&nbsp;</label><input type="range" id="precisao" name="precisao" min="1" max="8"
                        onchange="updateTextInput(this.value);">
                    <input type="text" id="textInput" style="max-width:50px;" value="5" disabled>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label>Limite Esquerdo:&nbsp;</label><input type="number" name="limiteEsquerdo" value="0">
                </div>
                <div class="col">
                    <label>Limite Direito:&nbsp;</label><input type="number" name="limiteDireito" value="0">
                </div>
                <div class="col">
                    <label>Nº de Intervalos:&nbsp;</label><input type="number" name="intervalo" value="1" min="1">
                </div>
            </div>
            <br>
            <button class="btn btn-success float-right" name="btnCalcular">Calcular</button>
            <h1><?php echo "Resultado: " . $string ?></h1>
            <?php if (isset($matrizFinal)): ?>
            <br>
            <h6>Tabela de Y:</h6>
            <table class="table">
                <tr>
                    <th>i</th>
                    <th>X</th>
                    <th>Y</th>
                </tr>
                <?php foreach ($matrizFinal as $matriz): ?>
                <?php echo ('<tr>'); ?>
                <?php foreach ($matriz as $array): ?>
                <?php echo ('<td>' . $array . '</td>'); ?>
                <?php endforeach;?>
                <?php echo ('</tr>'); ?>
                <?php endforeach;?>
                <?php endif;?>
            </table>
            <br>
            <br>
            <br>
            <h6>Dicas:</h6>
            <p>Para adicionar utilize <code>bcadd(Base, Número para somar)</code></p>
            <p>Para subtrair utilize <code>bcsub(Base, Número para diminuir)</code></p>
            <p>Para dividir utilize <code>bcdiv(Dividendo, Divisor)</code></p>
            <p>Para multiplicar utilize <code>bcmul(Base , Multiplicador)</code></p>
            <p>Para elevar utilize <code>bcpow(Base, Expoente)</code></p>
            <p>Para obter raiz quadrada utilize <code>bcsqrt(Número)</code></p>
            <p>Para utilizar o número de Euler (e), use <code>exp(expoente)</code></p>
            <p>Para utilizar o logaritmo use <code>log(argumento, base - opcional)</code>. Caso não seja informado base, será calculado o logaritmo niperiano do argumento.</p>
        </div>
    </form>

</body>

</html>