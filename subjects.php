<?php
// Funksioni për të ruajtur lëndët në një skedar JSON
function saveCourse($course_data) {
    $filename = 'courses.json';
    $courses = [];

    // Kontrollo nëse ekziston skedari dhe lexo të dhënat
    if (file_exists($filename)) {
        $json_data = file_get_contents($filename);
        $courses = json_decode($json_data, true);
    }

    // Shto lëndën e re në listë
    $courses[] = $course_data;

    // Ruaj të dhënat në skedar
    file_put_contents($filename, json_encode($courses, JSON_PRETTY_PRINT));
}

// Funksioni për të marrë të gjitha lëndët nga skedari JSON
function getCourses() {
    $filename = 'courses.json';
    if (file_exists($filename)) {
        $json_data = file_get_contents($filename);
        return json_decode($json_data, true);
    }
    return [];
}

// Funksioni për të fshirë lëndën nga skedari JSON
function deleteCourse($index) {
    $filename = 'courses.json';
    $courses = getCourses();

    // Kontrollo nëse indeksi është valid
    if (isset($courses[$index])) {
        unset($courses[$index]); // Fshi lëndën
        // Rregullo indeksin e array-it dhe ruaj ndryshimet
        $courses = array_values($courses);
        file_put_contents($filename, json_encode($courses, JSON_PRETTY_PRINT));
    }
}

$courses = getCourses();
$selected_program = $_GET['program'] ?? null; // Merr programin e zgjedhur nga URL-ja

// Kontrollo nëse është bërë kërkesë për fshirjen e një lënde
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_course'])) {
    $delete_index = $_POST['delete_course'];
    deleteCourse($delete_index);
}

// Kontrollo nëse është bërë kërkesë për të shtuar një lëndë
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_course'])) {
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];
    $course_hours = $_POST['course_hours'];
    $ects = $_POST['ects'];
    $course_status = $_POST['course_status'];
    $semester = $_POST['semester'];
    $program = $_POST['program']; // Merr programin nga forma

    // Krijo një array me të dhënat e lëndës
    $course_data = [
        'course_code' => $course_code,
        'course_name' => $course_name,
        'course_hours' => $course_hours,
        'ects' => $ects,
        'course_status' => $course_status,
        'semester' => $semester, // Merr semestrin nga forma
        'program' => $program
    ];

    // Ruaj lëndën në skedarin JSON
    saveCourse($course_data);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lëndët</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Stili mbetet i njëjtë si më parë */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #eaf6ff;
            color: #333;
        }

     

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .main-content h1 {
            margin: 0 0 20px;
            font-size: 24px;
            color: #00509e;
        }

        .actions {
            margin-bottom: 20px;
        }

        .actions button {
            background-color: #00509e;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin-right: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .actions button:hover {
            background-color: #004080;
        }

        .form-container {
            display: none;
            margin-top: 20px;
        }

        .form-container.active {
            display: block;
        }

        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .registered-courses {
            margin-top: 20px;
        }

        .registered-courses table {
            width: 100%;
            border-collapse: collapse;
        }

        .registered-courses table th, .registered-courses table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .registered-courses table th {
            background-color: #00509e;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Main Content -->
        <div class="main-content">
            <h1>Menaxhimi i Lëndëve</h1>
            <div class="actions">
                <form method="GET" style="margin-bottom: 20px;">
                </form>
                <button onclick="toggleForm()">Shto Lëndë</button>
            </div>
            <div class="form-container" id="form-container">
                <form method="POST" action="">
                    <input type="text" placeholder="Kodi i Lëndës" name="course_code" required>
                    <input type="text" placeholder="Emri i Lëndës" name="course_name" required>
                    <input type="text" placeholder="Fondi i Orëve" name="course_hours" required>
                    <input type="text" placeholder="ECTS" name="ects" required>
                    <label for="course_status">Statusi i lëndës:</label>
                    <select name="course_status" required>
                        <option value="O">Obligative</option>
                        <option value="Z">Zgjedhore</option>
                    </select>
                    <label for="semester">Semestri:</label>
                    <select name="semester" required>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                        <option value="VII">VII</option>
                        <option value="VIII">VIII</option>
                    </select>
                    <label for="program">Programi Studimor:</label>
                    <select name="program" required>
                        <option value="Informatikë">Informatikë</option>
                        <option value="Informatikë Mësimdhënie">Informatikë Mësimdhënie</option>
                        <option value="Programim i Aplikuar">Programim i Aplikuar</option>
                        <option value="Shkenca Kompjuterike dhe Inxhinieria">Shkenca Kompjuterike dhe Inxhinieria</option>
                    </select>
                    <button type="submit">Ruaj Lëndën</button>
                </form>
            </div>
            <div class="registered-courses">
                <h2>Lëndët për Programin: <?= htmlspecialchars($selected_program ?: 'Të gjitha') ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Kodi</th>
                            <th>Emri i Lëndës</th>
                            <th>Fondi i Orëve</th>
                            <th>ECTS</th>
                            <th>Statusi</th>
                            <th>Semestri</th>
                            <th>Programi Studimor</th>
                            <th>Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($courses as $index => $course) {
                            if ($selected_program && $course['program'] != $selected_program) {
                                continue;
                            }
                            echo "<tr>
                                <td>" . htmlspecialchars($course['course_code']) . "</td>
                                <td>" . htmlspecialchars($course['course_name']) . "</td>
                                <td>" . htmlspecialchars($course['course_hours']) . "</td>
                                <td>" . htmlspecialchars($course['ects']) . "</td>
                                <td>" . ($course['course_status'] == 'O' ? 'Obligative' : 'Zgjedhore') . "</td>
                                <td>" . htmlspecialchars($course['semester']) . "</td>
                                <td>" . htmlspecialchars($course['program']) . "</td>
                                <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='delete_course' value='$index'>
                                        <button type='submit' onclick='return confirm(\"A jeni të sigurt se doni të fshini këtë lëndë?\")'>Fshi</button>
                                    </form>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const formContainer = document.getElementById('form-container');
            formContainer.classList.toggle('active');
        }
    </script>
</body>
</html>
