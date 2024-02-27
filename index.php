<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nómina</title>
    <!-- Enlace a tu hoja de estilos CSS -->
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="nomina-container">
        <?php
        include_once 'calculoNomina.php';
        // Código PHP para conectarte a la base de datos y calcular la nómina
        $servername = "localhost";
        $username = "root";
        $password = ""; // Contraseña vacía como indicaste
        $dbname = "TablasSalariales2019"; // Asegúrate de cambiarlo por el nombre de tu base de datos
        
        // Crear conexión
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Verificar conexión
        if ($conn->connect_error) {
            die("La conexión ha fallado: " . $conn->connect_error);
        } else {
            echo "Conectado!";
        }
        
//Crear variables
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['horas'])) {
    $anioSeleccionado = $_POST['anio'];
    $horasSeleccionadas = $_POST['horas'];
    $trieniosSeleccionados = $_POST['trienios'];
    $pluses = isset($_POST['pluses']) ? $_POST['pluses'] : 0;
    $prorrateadas = $_POST['prorrateadas'] == "si" ? true : false;

    // Recuperar el valor del IRPF seleccionado
    $irpfSeleccionado = $_POST['irpf_hidden'];

    // Realizar la consulta para obtener los detalles de salario basados en las horas seleccionadas
    $sql = "SELECT * FROM Salarios WHERE Horas = $horasSeleccionadas";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Obtener los resultados en variables
        $row = $result->fetch_assoc();
        $horas = $row["Horas"];
        $salarioBase = $row["SalarioBase"];
        $plusTransporte = $row["PlusTransporte"];
        $trienio = $row["Trienio"];
        $totalTrienio = $row["Trienio"] * $trieniosSeleccionados;

        // Construir el nombre de la tabla basado en el año seleccionado
        $tablaSMI = "smi_" . $anioSeleccionado;

        // Preparar la consulta SQL
        $sql = "SELECT * FROM `$tablaSMI` WHERE Horas = $horasSeleccionadas";

        // Ejecutar la consulta
        $result = $conn->query($sql);

        // Verificar que se obtuvieron resultados
        if ($result->num_rows > 0) {
            // Obtener los datos de la fila
            $row = $result->fetch_assoc();
            
            // Determinar si se utiliza SMI12Pagas o SMI15Pagas basado en la elección del usuario
            if ($prorrateadas) {
                $smiSeleccionado = $row["SMI12Pagas"];
                $tipoDePagas = "12 pagas (prorrateadas)";
            } else {
                $smiSeleccionado = $row["SMI15Pagas"];
                $tipoDePagas = "15 pagas";
            }
            // Imprimir los resultados
            echo "<div class='resultado-salario'>";
            echo "<p>Cálculo de nómina para el año: $anioSeleccionado</p>";
            echo "<p>Horas: $horas</p>";
            echo "<p>Salario Base: €$salarioBase</p>";
            echo "<p>Plus Transporte: €$plusTransporte</p>";
            echo "<p>Trienio: €$trienio</p>";
            echo "<p>Precio Trienios: €$totalTrienio</p>";
            echo "<p>Pluses: €$pluses</p>";
            echo "<p>El IRPF que te quita la empresa es: $irpfSeleccionado%</p>";
            echo "El SMI para una jornada de $horasSeleccionadas horas en el año $anioSeleccionado, con las pagas $tipoDePagas, es: $smiSeleccionado €.";
            echo "</div>";
        } else {
            echo "No se encontraron datos para la jornada de $horasSeleccionadas horas en el año $anioSeleccionado.";
        }
    } else {
        echo "<p>No se encontraron resultados para las horas seleccionadas.</p>";
    }
   

    calculoNomina($salarioBase, $plusTransporte, $totalTrienio, $pluses, $irpfSeleccionado, $prorrateadas, $smiSeleccionado);

}
?>



<!--Formulario para obtener los datos-->
<form action="index.php" method="post">

<!--Año 2019 a 2022-->
<label for="anio">Selecciona el año:</label>
    <select name="anio" id="anio">
        <option value="2019">2019</option>
        <option value="2020">2020</option>
        <option value="2021">2021</option>
        <option value="2022">2022</option>
    </select>

<!--Obtener jornada semanal-->
    <label for="horas">Selecciona las horas semanales:</label>
    <select name="horas" id="horas">
        <?php
        // Asumiendo que ya has establecido la conexión a la base de datos $conn
        $sql = "SELECT Horas FROM Salarios ORDER BY Horas ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<option value='{$row["Horas"]}'>{$row["Horas"]}</option>";
            }
        } else {
            echo "<option>No hay datos disponibles</option>";
        }
        ?>
          </select>

    <!--Obtener los trienios--> 

          <label for="trienios">Selecciona los trienios (0-10):</label>
    <select name="trienios" id="trienios">
        <?php for($i = 0; $i <= 10; $i++): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>

    <!--Obtener los pluses--> 
    <label for="pluses">Introduce los pluses (0-500):</label>
    <input type="number" name="pluses" id="pluses" min="0" max="500">

    <!--Saber si tiene las pagas prorrateadas-->
    <label for="prorrateadas">Pagas prorrateadas:</label>
    <select name="prorrateadas" id="prorrateadas">
        <option value="no">No</option>
        <option value="si">Sí</option>
    </select> 
    
    <!--obtener irpf-->
    <label for="irpf">Seleccione el IRPF que le quita la empresa (%):</label><br>
  <input type="range" id="irpf" name="irpf" min="0" max="25" value="0" oninput="actualizarValor(this.value)">
  <span id="valorIRPF">0%</span><br>
  <!-- Agregamos un campo oculto para enviar el valor seleccionado -->
  <input type="hidden" id="irpf_hidden" name="irpf_hidden" value="0">


    <button type="submit">Consultar Salario</button>
</form>

    </div>
    <script src="validarPluses.js"></script>

</body>
</html>
