<?php
require '../includes/config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    // Or redirect to login
    die('Acceso no autorizado.');
}

$type = $_GET['type'] ?? '';
$filter_value = $_GET['filter_value'] ?? '';

if (empty($type) || empty($filter_value)) {
    die('Parámetros inválidos para generar el reporte.');
}

$rows = [];
$report_title = '';
$is_asesoria = false;

$params = [$filter_value];

if ($type === 'asesorias') {
    $is_asesoria = true;
    $report_title = "Reporte de Asesoría: " . htmlspecialchars($filter_value);
    $sql = "SELECT 
                tr.materia, 
                tr.nombres,
                tr.paterno,
                tr.materno,
                u.semestre, 
                u.carrera, 
                u.grupo, 
                u.turno 
            FROM tutoring_registrations tr
            LEFT JOIN users u ON tr.user_id = u.user_id
            WHERE tr.materia = ?
            ORDER BY u.carrera, u.semestre, u.grupo, tr.nombres, tr.paterno, tr.materno";
} else {
    $report_title = "Reporte de Club: " . htmlspecialchars($filter_value);
    $sql = "SELECT 
                cr.club_name, 
                cr.semestre, 
                u.carrera, 
                u.grupo, 
                u.turno,
                cr.nombres,
                cr.paterno,
                cr.materno
            FROM club_registrations cr
            LEFT JOIN users u ON cr.user_id = u.user_id
            WHERE cr.club_name = ? AND cr.club_type = ?
            ORDER BY u.carrera, cr.semestre, u.grupo, cr.nombres, cr.paterno, cr.materno";
    $params[] = $type;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($report_title) ?></title>
    <style>
        :root {
            --primary-color: #4D0011;
            --secondary-color: #62152d;
            --accent-color: #952f57;
            --white-color: #FFFFFF;
            --light-gray: #f4f7f6;
            --dark-gray: #333;
            --border-color: #e0e0e0;
        }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-gray);
        }
        .main-header.no-print {
            background-color: var(--primary-color);
            color: var(--white-color);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .main-header .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .main-header .logo span {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: background-color 0.2s, color 0.2s, border-color 0.2s;
        }
        .header-actions .btn {
            background-color: transparent;
            border-color: var(--white-color);
            color: var(--white-color);
        }
        .header-actions .btn:hover {
            background-color: var(--white-color);
            color: var(--primary-color);
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--white-color);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-top: 0;
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 0.5rem;
        }
        .controls.no-print {
            text-align: center;
            margin-bottom: 2rem;
        }
        .controls.no-print .btn {
            background-color: var(--accent-color);
            color: var(--white-color);
            margin: 0 0.5rem;
        }
        .controls.no-print .btn:hover {
            background-color: var(--secondary-color);
        }
        .controls.no-print .btn-secondary {
            background-color: #6c757d;
        }
        .controls.no-print .btn-secondary:hover {
            background-color: #5a6268;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid var(--border-color);
            padding: 0.75rem;
            text-align: left;
        }
        thead th {
            background-color: var(--secondary-color);
            color: var(--white-color);
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }
        @media print {
            body {
                background-color: var(--white-color);
            }
            .no-print {
                display: none;
            }
            .container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
            h1 {
                font-size: 1.5rem;
            }
            table {
                font-size: 0.9rem;
            }
            th, td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="main-header no-print">
        <div class="logo">
            <img src="../admin/assets/img/logo1.png" alt="Logo EduClubs" style="height: 50px;">
            <img src="https://cbtis258.edu.mx/wp-content/uploads/2024/08/cbtis258-logo.png" alt="Logo CBTis 258" style="height: 50px;">
            <span>Reporte</span>
        </div>
    </header>
    <div class="container">
        <h1><?= htmlspecialchars($report_title) ?></h1>
        <div class="controls no-print">
            <button class="btn" onclick="window.print()">Imprimir Reporte</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th><?php echo $is_asesoria ? 'Materia' : 'Club'; ?></th>
                    <th>Nombre Completo</th>
                    <th>Semestre</th>
                    <th>Carrera</th>
                    <th>Grupo</th>
                    <th>Turno</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No hay miembros para mostrar en este reporte.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo strtoupper(htmlspecialchars(($is_asesoria ? $row['materia'] : $row['club_name']) ?? '')); ?></td>
                            <td><?php echo strtoupper(htmlspecialchars(($row['nombres'] ?? '') . ' ' . ($row['paterno'] ?? '') . ' ' . ($row['materno'] ?? ''))); ?></td>
                            <td><?php echo strtoupper(htmlspecialchars($row['semestre'] ?? '')); ?></td>
                            <td><?php echo strtoupper(htmlspecialchars($row['carrera'] ?? '')); ?></td>
                            <td><?php echo strtoupper(htmlspecialchars($row['grupo'] ?? '')); ?></td>
                            <td><?php echo strtoupper(htmlspecialchars($row['turno'] ?? '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
