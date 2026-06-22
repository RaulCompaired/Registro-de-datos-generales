<?php
session_start();
require('../fpdf186/fpdf.php');
require 'conexion.php';

class PDF extends FPDF {
    
    function Header() {
        $this->SetDrawColor(106, 28, 50);
        $this->SetLineWidth(0.5);
        $this->Line(15, 8, $this->GetPageWidth() - 15, 8); 

        // Logos e imágenes
        $this->Image('../imagenes/ipn_logo2.png', 15, 12, 22);
        $this->Image('../imagenes/logo_escom.png', $this->GetPageWidth() - 37, 12, 22);
        
        // Título principal
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(106, 28, 50);
        $this->Ln(5);
        $this->Cell(0, 8, utf8_decode('Instituto Politécnico Nacional'), 0, 1, 'C');
        
        // Subtítulo
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 51, 153); // Color Azul ESCOM
        $this->Cell(0, 8, utf8_decode('Escuela Superior de Cómputo'), 0, 1, 'C');
        
        $this->Ln(8);
        
        $this->Line(15, 38, $this->GetPageWidth() - 15, 38);
        
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-25);
        
        $this->Image('../imagenes/logoEquipo.jpg', 15, $this->GetPageHeight() - 22, 10);
        
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(128, 128, 128);
        
        $this->SetX(30);
        $fecha_hoy = date("d/m/Y");
        $this->Cell(0, 10, 'Fecha: ' . $fecha_hoy, 0, 0, 'L');
        
        $this->SetX(10);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }
}

$boleta = '';
if (isset($_SESSION['boleta'])) {
    $boleta = $_SESSION['boleta'];
} else if (isset($_GET['boleta'])) {
    $boleta = $_GET['boleta'];
} else {
    echo "No autorizado";
    exit;
}

$boleta = mysqli_real_escape_string($conexion, $boleta);
$consulta = mysqli_query($conexion, "SELECT a.*, g.nombre AS grupo_nombre, g.hora_inicio, g.hora_fin, g.fecha_examen, l.nombre AS laboratorio_nombre
    FROM alumnos a
    LEFT JOIN grupos g ON a.grupo_id = g.id
    LEFT JOIN laboratorios l ON g.laboratorio_id = l.id
    WHERE a.boleta = '$boleta'");
$alumno = mysqli_fetch_assoc($consulta);

if (!$alumno) {
    echo "Alumno no encontrado";
    exit;
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('ACUSE DE REGISTRO - EXAMEN DIAGNÓSTICO'), 0, 1, 'C');
$pdf->Ln(5);

$ancho_etiq = 60;  
$ancho_dato = 110;  
$margen_izq = 20; 

// --- SECCIÓN 1: DATOS PERSONALES Y PROCEDENCIA ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 51, 153); // Azul ESCOM para subtítulo
$pdf->SetX($margen_izq);
$pdf->Cell(0, 8, utf8_decode('Datos del Alumno'), 0, 1, 'L');
$pdf->SetDrawColor(0, 51, 153);
$pdf->Line($margen_izq, $pdf->GetY(), $pdf->GetPageWidth() - 20, $pdf->GetY());
$pdf->Ln(4);

$pdf->SetTextColor(0, 0, 0);

$escuela_show = $alumno['escuela_procedencia'];
if ($escuela_show === 'Otro' && !empty($alumno['nombre_escuela'])) {
    $escuela_show = $alumno['nombre_escuela'];
}

$datos_personales = [
    'Nombre completo:' => $alumno['nombre'],
    'No. de Boleta:' => $alumno['boleta'],
    'Fecha de Nacimiento:' => date("d/m/Y", strtotime($alumno['fecha_nacimiento'])),
    'Género:' => $alumno['genero'],
    'CURP:' => $alumno['curp'],
    'Entidad Federativa:' => $alumno['entidad_federativa'],
    'Teléfono:' => $alumno['telefono'] ? $alumno['telefono'] : 'N/A',
    'Escuela de Procedencia:' => $escuela_show,
    'Promedio de Bachillerato:' => $alumno['promedio'],
    'Correo Institucional:' => $alumno['correo']
];

foreach ($datos_personales as $etiqueta => $valor) {
    $pdf->SetX($margen_izq);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ancho_etiq, 7, utf8_decode($etiqueta), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell($ancho_dato, 7, utf8_decode($valor), 0, 1, 'L');
}

$pdf->Ln(6);

// --- SECCIÓN 2: DETALLES DE EXAMEN (RESALTADOS) ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 51, 153); // Azul ESCOM
$pdf->SetX($margen_izq);
$pdf->Cell(0, 8, utf8_decode('Detalles de la Cita para el Examen Diagnóstico'), 0, 1, 'L');
$pdf->SetDrawColor(0, 51, 153);
$pdf->Line($margen_izq, $pdf->GetY(), $pdf->GetPageWidth() - 20, $pdf->GetY());
$pdf->Ln(4);

// Fondo gris claro para resaltar la tarjeta de examen
$pdf->SetFillColor(245, 245, 245);
$pdf->SetDrawColor(180, 180, 180);
$pdf->SetX($margen_izq);

// Calcular la altura y dibujar el rectángulo de fondo de la cita
$y_inicio = $pdf->GetY();
$y_fin = $y_inicio + 38; // Estimado para 4 filas de datos con padding

$pdf->Rect($margen_izq, $y_inicio, $pdf->GetPageWidth() - 40, $y_fin - $y_inicio, 'DF');

// Imprimir los datos del examen dentro del área resaltada
// Grupo
$pdf->SetY($y_inicio + 3);
$pdf->SetX($margen_izq + 5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(128, 0, 32); // Guinda IPN para resaltar
$pdf->Cell($ancho_etiq - 5, 8, utf8_decode('Grupo Asignado:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($ancho_dato - 5, 8, utf8_decode($alumno['grupo_nombre'] ?? 'Sin grupo'), 0, 1, 'L');

// Horario
$horario_show = $alumno['grupo_nombre'] ? (date("H:i", strtotime($alumno['hora_inicio'])) . ' a ' . date("H:i", strtotime($alumno['hora_fin']))) : 'N/A';
$pdf->SetX($margen_izq + 5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(128, 0, 32); // Guinda IPN para resaltar
$pdf->Cell($ancho_etiq - 5, 8, utf8_decode('Horario de Examen:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($ancho_dato - 5, 8, utf8_decode($horario_show), 0, 1, 'L');

// Laboratorio (normal, negro)
$pdf->SetTextColor(0, 0, 0);
$pdf->SetX($margen_izq + 5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($ancho_etiq - 5, 8, utf8_decode('Laboratorio asignado:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($ancho_dato - 5, 8, utf8_decode($alumno['laboratorio_nombre'] ?? 'N/A'), 0, 1, 'L');

// Fecha Examen (normal, negro)
$fecha_examen_show = $alumno['grupo_nombre'] ? date("d/m/Y", strtotime($alumno['fecha_examen'])) : 'N/A';
$pdf->SetX($margen_izq + 5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($ancho_etiq - 5, 8, utf8_decode('Fecha de Examen:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($ancho_dato - 5, 8, utf8_decode($fecha_examen_show), 0, 1, 'L');

$pdf->Output('D', 'Reporte_' . $boleta . '.pdf');
exit;
?>