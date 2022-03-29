<?php

require_once 'funcoes.php';
$string = (float) null;
$funcao = $_POST["funcao"];
$metodo = $_POST["metodo"];

//Limite esquerdo -> inferior
$limiteEsquerdo = $_POST["limiteEsquerdo"];

//Limite direito -> superior
$limiteDireito = $_POST["limiteDireito"];
$intervalo = $_POST["intervalo"];

//variaveis para extrapolação de richarlison
$metodoRicharlisson = $_POST["metodoRicharlisson"];
$intervalo2 = $_POST["intervalo2"];

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
        $erro = erroTrapezio($h, $limiteDireito, $limiteEsquerdo, $funcao);

    } else if ($metodo == "3S") {

        $h = calculaH($limiteDireito, $limiteEsquerdo, $intervalo);
        $matrizFinal = tabelaY($limiteEsquerdo, $limiteDireito, $h, $intervalo, $funcao);
        $y = valorY($matrizFinal);
        $string = tresOitavosSimpson($y, $h);
        //$erro = erroTresOitavosSimpson($funcao, $h);

    } else if ($metodo == "ER") {

        $h1 = calculaH($limiteDireito, $limiteEsquerdo, $intervalo);
        $matriz1 = tabelaY($limiteEsquerdo, $limiteDireito, $h1, $intervalo, $funcao);
        $y1 = valorY($matriz1);

        $h2 = calculaH($limiteDireito, $limiteEsquerdo, $intervalo2);
        $matriz2 = tabelaY($limiteEsquerdo, $limiteDireito, $h2, $intervalo2, $funcao);
        $y2 = valorY($matriz2);

        if ($metodoRicharlisson = 1) {

            $resultado1 = trapezio($y1, $h1);
            $resultado2 = trapezio($y2, $h2);

            $string = extrapolacaoRicharlison($metodoRicharlisson, $intervalo, $resultado1, $intervalo2, $resultado2);
        } else {
            if ($metodoRicharlisson = 2) {

                $resultado1 = tercoDeSimpson($y1, $h1);
                $resultado2 = tercoDeSimpson($y2, $h2);

                $string = extrapolacaoRicharlison(2, $intervalo, $resultado1, $intervalo2, $resultado2);
            } else {

                $resultado1 = tresOitavosSimpson($y1, $h1);

                //$erro = erroTresOitavosSimpson($resultado1, $h1);

                $resultado2 = tresOitavosSimpson($y2, $h2);

                $string = extrapolacaoRicharlison(2, $intervalo, $resultado1, $intervalo2, $resultado2);
            }
        }
    }

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
    <script>
    function verificarRicharlison(val) {
        if (val == "ER") {
            document.getElementById('metodoRicharlisson').style.display = 'block';
            document.getElementById('intervaloRicharlison').style.display = 'block';
        } else {
            document.getElementById('metodoRicharlisson').style.display = 'none';
            document.getElementById('intervaloRicharlison').style.display = 'none';
        }
    }
    </script>
    <br>
    <br>

    <form method="POST">
        <div class="container">
            <div class="row">
                <div class="col">
                    <select name="metodo" id="metodo" onchange="verificarRicharlison(this.value);">
                        <option value="1S">1/3 de Simpson</option>
                        <option value="3S">3/8 de Simpson</option>
                        <option value="ER">Extrapolação de Richarlison</option>
                        <option value="RT">Regra dos Trapézios</option>
                        <option value="QG">Quadratura Gauciana</option>
                    </select>
                    <select style="display: none;" name="metodoRicharlisson" id="metodoRicharlisson">
                        <option value="1">Regra dos Trapézios</option>
                        <option value="2">1/3 de Simpson</option>
                        <option value="3">3/8 de Simpson</option>
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
                    <div id="intervaloRicharlison" style="display: none;">
                        <label>Nº de Intervalos segunda função:&nbsp;</label><input type="number" name="intervalo2"
                            value="1" min="1">
                    </div>
                </div>
            </div>
            <br>
            <button class="btn btn-success float-right" name="btnCalcular">Calcular</button>
            <h1><?php echo "Resultado: " . $string ?></h1>
            <?php if (isset($matriz1)): ?>
            <br>
            <h3><?php echo ("Resultado primeira: " . $resultado1); ?></h3>
            <h6>Tabela de Y primeira:</h6>
            <table class="table">
                <tr>
                    <th>i</th>
                    <th>X</th>
                    <th>Y</th>
                </tr>
                <?php foreach ($matriz1 as $matriz): ?>
                    <?php echo ('<tr>'); ?>
                    <?php foreach ($matriz as $array): ?>
                    <?php echo ('<td>' . $array . '</td>'); ?>
                    <?php endforeach;?>
                    <?php echo ('</tr>'); ?>
                <?php endforeach;?>
            <?php endif;?>
            </table>
            <?php if (isset($matriz2)): ?>
                <br>
                <?php echo ("<h3>Resultado segunda: " . $resultado2 . "</h3>"); ?>
                <h6>Tabela de Y segunda:</h6>
                <table class="table">
                    <tr>
                        <th>i</th>
                        <th>X</th>
                        <th>Y</th>
                    </tr>
                    <?php foreach ($matriz2 as $matriz): ?>
                    <?php echo ('<tr>'); ?>
                    <?php foreach ($matriz as $array): ?>
                    <?php echo ('<td>' . $array . '</td>'); ?>
                    <?php endforeach;?>
                    <?php echo ('</tr>'); ?>
                <?php endforeach;?>
            <?php endif;?>
            </table>
                    <h3><?php if (isset($erro)) {echo ("Erro: " . $erro);}?></h3>

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
                    <p>Para utilizar o logaritmo use <code>log(argumento, base - opcional)</code>. Caso não seja
                        informado base,
                        será calculado o logaritmo niperiano do argumento.</p>
        </div>
    </form>

</body>

</html>