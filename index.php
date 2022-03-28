<?php
$string = (float) null;
$funcao = $_POST["funcao"];
$metodo = $_POST["metodo"];
$limiteEsquerdo = $_POST["limiteEsquerdo"];
$limiteDireito = $_POST["limiteDireito"];
$intervalo = $_POST["intervalo"];

if (isset($_POST['btnCalcular'])) {
    bcscale($_POST["precisao"]);
    //$string = bc($funcao, "0.3", "0.2");
    $string = eval('return ' . $funcao . ';');
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
            <br>
            <br>
            <br>
            <h6>Dicas:</h6>
            <p>Para adicionar utilize bcadd(Base, Número para somar)</p>
            <p>Para subtrair utilize bcsub(Base, Número para diminuir)</p>
            <p>Para dividir utilize bcdiv(Dividendo, Divisor)</p>
            <p>Para multiplicar utilize bcmul(Base , Multiplicador)</p>
            <p>Para elevar utilize bcpow(Base, Expoente)</p>
            <p>Para obter raiz quadrada utilize bcsqrt(Número)</p>
        </div>
    </form>

</body>

</html>