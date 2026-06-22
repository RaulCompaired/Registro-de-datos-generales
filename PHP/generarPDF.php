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

if (!isset($_SESSION['boleta'])) {
    echo "No autorizado";
    exit;
}

$boleta = $_SESSION['boleta'];
$consulta = mysqli_query($conexion, "SELECT a.*, g.nombre AS grupo_nombre, g.hora_inicio, g.hora_fin, g.fecha_examen, l.nombre AS laboratorio_nombre
    FROM alumnos a
    LEFT JOIN grupos g ON a.grupo_id = g.id
    LEFT JOIN laboratorios l ON g.laboratorio_id = l.id
    WHERE a.boleta = '$boleta'");
$alumno = mysqli_fetch_assoc($consulta);

$row_db = [
    "boleta"  => $alumno['boleta'],
    "alumno"  => $alumno['nombre'],
    "fecha_nacimiento" => $alumno['fecha_nacimiento'],
    "correo"  => $alumno['correo'],
    "curp"    => $alumno['curp'],
    "grupo"   => $alumno['grupo_nombre'],
    "hora_inicio" => $alumno['hora_inicio'],
    "hora_fin" => $alumno['hora_fin'],
    "fecha_examen" => $alumno['fecha_examen'],
    "laboratorio" => $alumno['laboratorio_nombre']
];

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Registro de Estudiante', 0, 1, 'C');
$pdf->Ln(8);

$ancho_etiq = 60;  
$ancho_dato = 90;  
$margen_izq = 25; 

// --- DATOS DEL ALUMNO ---
$datos_alumno = [
    'Alumno:' => $row_db['alumno'],
    'Boleta:' => $row_db['boleta'],
    'Fecha de Nacimiento:' => $row_db['fecha_nacimiento'],
    'Correo:' => $row_db['correo'],
    'CURP:' => $row_db['curp'],
    'Grupo:' => $row_db['grupo']
];

foreach ($datos_alumno as $etiqueta => $valor) {
    $pdf->SetX($margen_izq);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($ancho_etiq, 10, utf8_decode($etiqueta), 0, 0, 'R');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell($ancho_dato, 10, utf8_decode($valor), 0, 1, 'L');
}

$pdf->Ln(10);

$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(40, $pdf->GetY(), $pdf->GetPageWidth() - 40, $pdf->GetY());
$pdf->Ln(10);

// --- INFORMACIÓN DE EXAMEN ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 51, 153); // Azul ESCOM para subtítulo
$pdf->Cell(0, 10, utf8_decode('Información de Examen'), 0, 1, 'C'); 
$pdf->Ln(5);
$pdf->SetTextColor(0, 0, 0);

$datos_examen = [
    'Hora de Inicio:' => $row_db['hora_inicio'],
    'Hora de fin:' => $row_db['hora_fin'],
    'Fecha de Examen:' => $row_db['fecha_examen'],
    'Laboratorio:' => $row_db['laboratorio']
];

// Definimos un color gris muy clarito para el fondo
$pdf->SetFillColor(240, 240, 240); 

foreach ($datos_examen as $etiqueta => $valor) {
    $pdf->SetX($margen_izq);
    
    $pdf->SetFont('Arial', 'B', 12);
    // El 'true' al final enciende el color de fondo
    $pdf->Cell($ancho_etiq, 10, utf8_decode($etiqueta), 0, 0, 'R', true);
    
    $pdf->SetFont('Arial', '', 12);
    // El dato se queda en 'false' (sin fondo)
    $pdf->Cell($ancho_dato, 10, utf8_decode($valor), 0, 1, 'L', true);
}

$pdf->Output('D', 'Reporte_' . $boleta . '.pdf');
?>