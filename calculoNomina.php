<?php
// calculoNomina.php
function calculoNomina($salarioBase, $plusTransporte, $trienio, $pluses, $irpf, $prorrateadas, $smiSeleccionado) {
    echo "<h2>Detalle de la Nómina</h2>";

    // Sumar salario base, antigüedad (trienio), plus transporte y pluses
    $salarioBruto = (float)$salarioBase + (float)$plusTransporte + (float)$trienio + (float)$pluses;
    echo "Salario Bruto: $salarioBruto<br>";

    // Verificar si el salario bruto alcanza el SMI
    $complementoSMI = 0;
    if ($salarioBruto < $smiSeleccionado) {
        $complementoSMI = $smiSeleccionado - $salarioBruto;
        $salarioBruto = $smiSeleccionado;
        echo "Complemento hasta SMI: $complementoSMI<br>";
    }

    // Definir porcentajes de deducciones
    $contingenciasComunesPorcentaje = 4.7;
    $formacionProfesionalPorcentaje = 0.1;
    $desempleoPorcentaje = 1.55;

    // Calcular deducciones
    $contingenciasComunes = $salarioBruto * ($contingenciasComunesPorcentaje / 100);
    echo "Contingencias Comunes (4.7%): $contingenciasComunes<br>";
    
    $formacionProfesional = $salarioBruto * ($formacionProfesionalPorcentaje / 100);
    echo "Formación Profesional (0.1%): $formacionProfesional<br>";
    
    $desempleo = $salarioBruto * ($desempleoPorcentaje / 100);
    echo "Desempleo (1.55%): $desempleo<br>";
    
    $irpfDeduccion = $salarioBruto * ($irpf / 100);
    echo "IRPF ($irpf%): $irpfDeduccion<br>";

    // Calcular total deducciones
    $totalDeducciones = $contingenciasComunes + $formacionProfesional + $desempleo + $irpfDeduccion;
    echo "Total Deducciones: $totalDeducciones<br>";

    // Calcular salario neto
    $salarioNeto = $salarioBruto - $totalDeducciones;
    echo "Salario Neto: $salarioNeto<br>";

    // No es necesario devolver el salario neto si ya se está imprimiendo todo
}
