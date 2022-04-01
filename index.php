<?php

require_once 'funcoes.php';
$string = (float) null;
$funcao = isset($_POST["funcao"]) ? $_POST["funcao"] : null;
$metodo = isset($_POST["metodo"]) ? $_POST["metodo"] : null;
if ($funcao && !(str_contains($funcao, "X"))) {
    echo ("Você não informou X na sua função!");
    die();
}
//Limite Inferior -> inferior
$limiteInferior = isset($_POST["limiteInferior"]) ? $_POST["limiteInferior"] : null;

//Limite Superior -> superior
$limiteSuperior = isset($_POST["limiteSuperior"]) ? $_POST["limiteSuperior"] : null;
$intervalo = isset($_POST["intervalo"]) ? $_POST["intervalo"] : null;

//variaveis para extrapolação de richarlison
$metodoRicharlisson1 = isset($_POST["metodoRicharlisson1"]) ? $_POST["metodoRicharlisson1"] : null;
$metodoRicharlisson2 = isset($_POST["metodoRicharlisson2"]) ? $_POST["metodoRicharlisson2"] : null;
$yInformado = (isset($_POST["valorY"])) ? $_POST["valorY"] : null;
$intervalo2 = isset($_POST["intervalo2"]) ? $_POST["intervalo2"] : null;

if (isset($_POST['btnCalcular'])) {
    bcscale($_POST["precisao"]);
    if ($metodo == "1S") {
        $h = calculaH($limiteSuperior, $limiteInferior, $intervalo);
        if (isset($yInformado)) {
            $y = $yInformado;
            $string = round(tercoDeSimpson($y, $h, $intervalo), $_POST["precisao"]);
        } else {
            $matrizFinal = tabelaY($limiteInferior, $limiteSuperior, $h, $intervalo, $funcao);
            $y = valorY($matrizFinal);
            $string = round(tercoDeSimpson($y, $h, $intervalo), $_POST["precisao"]);
            // $erro = erroTercoSimpson($funcao, $h, $string);
        }
    } else if ($metodo == "QG") {

        $string = round(quadraturaGaussiana($limiteInferior, $limiteSuperior, $intervalo, $funcao), $_POST["precisao"]);

    } else if ($metodo == "RT") {

        $h = calculaH($limiteSuperior, $limiteInferior, $intervalo);
        if (isset($yInformado)) {
            $y = $yInformado;
        } else {
            $matrizFinal = tabelaY($limiteInferior, $limiteSuperior, $h, $intervalo, $funcao);
            $y = valorY($matrizFinal);
            $erro = erroTrapezio($h, $limiteSuperior, $limiteInferior, $funcao);
        }
        $string = round(trapezio($y, $h), $_POST["precisao"]);

    } else if ($metodo == "3S") {

        $h = calculaH($limiteSuperior, $limiteInferior, $intervalo);
        if (isset($yInformado)) {
            $y = $yInformado;
        } else {
            $matrizFinal = tabelaY($limiteInferior, $limiteSuperior, $h, $intervalo, $funcao);
            $y = valorY($matrizFinal);
            $erro = erroTresOitavosSimpson($funcao, $h);
        }
        $string = round(tresOitavosSimpson($y, $h, $intervalo), $_POST["precisao"]);

    } else if ($metodo == "ER") {

        $h1 = calculaH($limiteSuperior, $limiteInferior, $intervalo);
        $h2 = calculaH($limiteSuperior, $limiteInferior, $intervalo2);
        $teste = $intervalo2 / $intervalo;
        $y1 = [];
        if (isset($yInformado)) {
            for ($i = 0; $i < count($yInformado); $i++) {
                if ($i % $teste == 0) {
                    array_push($y1, $yInformado[$i]);
                }
            }
            $y2 = $yInformado;
        } else {
            $matriz1 = tabelaY($limiteInferior, $limiteSuperior, $h1, $intervalo, $funcao);
            $y1 = valorY($matriz1);

            $matriz2 = tabelaY($limiteInferior, $limiteSuperior, $h2, $intervalo2, $funcao);
            $y2 = valorY($matriz2);
        }
        if ($metodoRicharlisson1 == 1) {
            $identificador = 1;
            $resultado1 = trapezio($y1, $h1);
        } else if ($metodoRicharlisson1 == 2) {
            $identificador = 2;
            $resultado1 = tercoDeSimpson($y1, $h1);
        } else if ($metodoRicharlisson1 == 3) {
            $identificador = 2;
            $resultado1 = tresOitavosSimpson($y1, $h1);
        }
        if ($metodoRicharlisson2 == 1) {
            $identificador = 1;
            $resultado2 = trapezio($y2, $h2);
        } else if ($metodoRicharlisson2 == 2) {
            $identificador = 2;
            $resultado2 = tercoDeSimpson($y2, $h2);
        } else if ($metodoRicharlisson2 == 3) {
            $identificador = 2;
            $resultado2 = tresOitavosSimpson($y2, $h2);
        }
        $string = round(extrapolacaoRicharlison($identificador, $intervalo, $resultado1, $intervalo2, $resultado2), $_POST["precisao"]);
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
            document.getElementById('labelRicharlisson1').style.display = 'block';
            document.getElementById('labelRicharlisson2').style.display = 'block';
            document.getElementById('metodoRicharlisson1').style.display = 'block';
            document.getElementById('metodoRicharlisson2').style.display = 'block';
            document.getElementById('intervaloRicharlison').style.display = 'block';
        } else {
            document.getElementById('labelRicharlisson1').style.display = 'none';
            document.getElementById('labelRicharlisson2').style.display = 'none';
            document.getElementById('metodoRicharlisson1').style.display = 'none';
            document.getElementById('metodoRicharlisson2').style.display = 'none';
            document.getElementById('intervaloRicharlison').style.display = 'none';
        }
    }
    </script>
    <script>
    function criarTabelaValorY(checked) {

        if (checked) {
            var numero = document.getElementById("intervalo").value;
            var numero2 = document.getElementById("intervalo2").value;
            if (numero2 > numero) {
                numero = numero2;
            }
            for (let i = 0; i <= numero; i = i + 1 ) {
                var container = document.getElementById("divTabelaY");
                var input = document.createElement("input");
                input.type = "number";
                input.name = "valorY[]"
                input.className= "form-control";
                input.step = "any"
                container.appendChild(input);
            }
        } else {
            var ele = document.getElementsByName("valorY[]");
            len = ele.length;
            parentNode = ele[0].parentNode;
            for (var i=0; i<len; i++) {
            parentNode.removeChild(ele[0]);
            }
        }

    }
    </script>
    <br>
    <br>

    <form method="POST">
        <div class="container">
            <div class="row">
                <div class="col">
                    <label>Método:&nbsp;</label>
                    <select name="metodo" id="metodo" onchange="verificarRicharlison(this.value);">
                        <option value="1S">1/3 de Simpson</option>
                        <option value="3S">3/8 de Simpson</option>
                        <option value="ER">Extrapolação de Richarlison</option>
                        <option value="RT">Regra dos Trapézios</option>
                        <option value="QG">Quadratura Gauciana</option>
                    </select>
                    <label style="display: none;" id="labelRicharlisson1">Método da 1ª</label>
                    <select style="display: none;" name="metodoRicharlisson1" id="metodoRicharlisson1">
                        <option value="1">Regra dos Trapézios</option>
                        <option value="2">1/3 de Simpson</option>
                        <option value="3">3/8 de Simpson</option>
                    </select>
                    <label style="display: none;" id="labelRicharlisson2">Método da 2ª</label>
                    <select style="display: none;" name="metodoRicharlisson2" id="metodoRicharlisson2">
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
                    <label>Limite Inferior:&nbsp;</label><input type="number" step="any" name="limiteInferior" value="0">
                </div>
                <div class="col">
                    <label>Limite Superior:&nbsp;</label><input type="number" step="any" name="limiteSuperior" value="0">
                </div>
                <div class="col">
                    <label>Nº de Intervalos:&nbsp;</label><input type="number" name="intervalo" id="intervalo" value="1" min="1">
                    <div id="intervaloRicharlison" style="display: none;">
                    <label>Nº de Intervalos 2ª:&nbsp;</label><input type="number" name="intervalo2" id="intervalo2" value="1" min="1">
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <input type="checkbox" id="definirValorY" onclick="criarTabelaValorY(this.checked);"><label>&nbsp;Inserir valor de Y</label>
                </div>
                <div class="col">
                <button class="btn btn-success float-right" name="btnCalcular">Calcular</button>
                </div>
            </div>
            <div id="divTabelaY" class="form-group col-md-2">

            </div>
            <h1><?php echo "Resultado: " . $string ?></h1>
            <?php if (isset($resultado1)): ?>
            <br>
            <h3><?php echo ("Resultado primeira: " . $resultado1); ?></h3>
            <?php if (isset($matriz1)): ?>
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
            <?php endif;?>
            </table>
            <?php if (isset($resultado2)): ?>
                <br>
                <?php echo ("<h3>Resultado segunda: " . $resultado2 . "</h3>"); ?>
                <?php if (isset($matriz2)): ?>
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
                    <p><b>Sempre utilizar o X em maiúsculo, pois pode ocorrer conflitos com outras funções</b></p>
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