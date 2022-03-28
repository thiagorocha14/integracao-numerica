<?

?>
<html>

<head>
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

    <form>
        <div class="container">
            <div class="row">
                <div class="col">
                    <select>
                        <option value="RS">1ª Regra de Simpson</option>
                        <option value="ER">Extrapolação de Richarlison</option>
                        <option value="RT">Regra dos Trapézios</option>
                        <option value="G">Quadratura Gauciana</option>
                    </select>
                </div>
                <div class="col">
                    <label>Função:&nbsp;</label><input type="text" placeholder="Função">
                </div>
                <div class="col">
                    <label>Precisão: </label><input type="range" id="points" name="points" min="1" max="8"
                        onchange="updateTextInput(this.value);">
                    <input type="text" id="textInput" style="max-width:50px;" value="5" disabled>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label>Limite Esquerdo:&nbsp;</label><input type="number" value="0">
                </div>
                <div class="col">
                    <label>Limite Direito:&nbsp;</label><input type="number" value="0">
                </div>
                <div class="col">
                    <label>Nº de Intervalos:&nbsp;</label><input type="number" value="1" min="1">
                </div>
            </div>
            <button class="btn btn-success">Calcular</button>
        </div>
    </form>
</body>

</html>