<?php
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
        $alturaFinal = (double) ($limiteDireito - $limiteEsquerdo) / $intervalo;
        $matrizFinal = tabelaY($limiteEsquerdo, $limiteDireito, $alturaFinal, $intervalo, $funcao);
        $y = valorY($matrizFinal);
        $string = tercoDeSimpson($y, $alturaFinal);
    }

    if ($metodo == "RT") {
        $h = calculaH($limDireito, $limEsquerdo, $intervalo);
        $matrizFinal = tabelaY($limEsquerdo, $limDireito, $h, $intervalo, $funcao);
        $y = valorY($matrizFinal);
        $string = trapezio($y, $h);
    }

}

function calculaH($limSuperior, $limInferior, $nIntervalos) {
    $h = ($limSuperior - $limInferior) / $nIntervalos;
    return $h;
}

function quadraturaGaussiana($limEsquerdo, $limDireito, $numero, $integral)
{
    $retorno = "";
    if ($limEsquerdo == "-1" && $limDireito == "1") {
        $integral = str_replace("dx", "", $integral);

    }
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

// Ainda não funcionando corretamente
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

// Ainda não funcionando corretamente
function erroTrapezio(float $h, float $limSup, float $limInf, string $derivada) {
    $result = (double) 0;
    $retFinal = (string) $derivada.str_replace("x", "3");
    $devF = (double) eval('return ' . $retFinal . ';');
    $result = (-((($limSup - $limInf) * bcpow($h, "2")) / 12 )) * $devF;

    return $result;
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
            <? if(isset($matrizFinal)) { ?>
            <h6>Tabela de Y:</h6>
            <? $counttoken = count($token);
                $k=0;
                foreach($token as $key=>$value)
                    {
                        echo "<tr><td>$value</td>";
                        for($i=0; $i<$counttoken;$i++)
                        {
                            echo "<td>" .$num[$k++]. "</td>";
                        }
                }   ?>
            <? } ?>
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