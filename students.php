<?php
ob_start(); // Starton buffering për të shmangur problemet me header-at

// Kontrollimi i dërgimit të formularit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Marrja e të dhënave nga formulari
    if (isset($_POST['add_student'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $parent_name = $_POST['parent_name'];
        $birth_date = $_POST['birth_date'];
        $personal_number = $_POST['personal_number'];
        $place = $_POST['place'];
        $state = $_POST['state'];
        $citizenship = $_POST['citizenship'];
        $academic_year = $_POST['academic_year'];
        $study_program = $_POST['study_program'];
        $study_cycle = $_POST['study_cycle'];
        $semester = $_POST['semester'];

        // Ruajtja e studentëve në një skedar JSON
        $student_data = [
            'id' => $id,
            'name' => $name,
            'parent_name' => $parent_name,
            'birth_date' => $birth_date,
            'personal_number' => $personal_number,
            'place' => $place,
            'state' => $state,
            'citizenship' => $citizenship,
            'academic_year' => $academic_year,
            'study_program' => $study_program,
            'study_cycle' => $study_cycle,
            'semester' => $semester,

            
        ];

        // Leximi dhe ruajtja e të dhënave të mëparshme
        $students_file = 'students.json';
        $students = file_exists($students_file) ? json_decode(file_get_contents($students_file), true) : [];
        $students[] = $student_data;

        // Ruajtja e të dhënave të reja
        file_put_contents($students_file, json_encode($students, JSON_PRETTY_PRINT));

        // Redirect për të shmangur ri-dërgimin e formularit
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Fshirja e studentit
    if (isset($_POST['delete_student'])) {
        $student_id = $_POST['student_id'];
        $students = file_exists('students.json') ? json_decode(file_get_contents('students.json'), true) : [];
        $students = array_filter($students, function ($student) use ($student_id) {
            return $student['id'] !== $student_id;
        });
        file_put_contents('students.json', json_encode(array_values($students), JSON_PRETTY_PRINT));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Ndryshimi i të dhënave të studentit
    if (isset($_POST['edit_student'])) {
        $student_id = $_POST['student_id'];
        $students = file_exists('students.json') ? json_decode(file_get_contents('students.json'), true) : [];
        foreach ($students as &$student) {
            if ($student['id'] === $student_id) {
                // Përditësimi i të dhënave të studentit
                $student['name'] = $_POST['name'];
                $student['parent_name'] = $_POST['parent_name'];
                $student['birth_date'] = $_POST['birth_date'];
                $student['personal_number'] = $_POST['personal_number'];
                $student['place'] = $_POST['place'];
                $student['state'] = $_POST['state'];
                $student['citizenship'] = $_POST['citizenship'];
                $student['academic_year'] = $_POST['academic_year'];
                $student['study_program'] = $_POST['study_program'];
                $student['study_cycle'] = $_POST['study_cycle'];
                $student['semester'] = $_POST['semester'];
                break;
            }
        }
        file_put_contents('students.json', json_encode($students, JSON_PRETTY_PRINT));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
// Leximi i studentëve ekzistues
$students = file_exists('students.json') ? json_decode(file_get_contents('students.json'), true) : [];

// qeket ekam shtu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_name'])) {
    $student_id = $_POST['student_id'];
    $course_name = $_POST['course_name'];
    $grade = $_POST['grade']; // Default grade to null when adding a course

    // Update student data with selected course
    $students = file_exists('students.json') ? json_decode(file_get_contents('students.json'), true) : [];
    foreach ($students as &$student) {
        if ($student['id'] === $student_id) {
            // Add course with selected course name and grade
            $student['courses'][] = [
                'selected_course' => $course_name,
                'grade' => $grade 
            ];
            break;
        }
    }

    // Save updated student data back to the file
    file_put_contents('students.json', json_encode($students, JSON_PRETTY_PRINT));

    // Redirect to refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menaxhimi i Studentëve</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .search-container {
            margin-bottom: 10px;
        }
        .search-container input {
            width: 20%; 
            padding: 5px; 
            font-size: 13px; 
            border: 1px solid #aaa; 
            border-radius: 5px; 
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); 
        }
        .form-container {
            display: none;
        }
        .form-container.active {
            display: block;
        }
        button {
            margin-top: 5px;
        }
        .student-details, .edit-form-container {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            width: 600px;
        }
        .student-details.active, .edit-form-container.active {
            display: block;
        }
        .university-name {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .faculty-name {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .student-details p, .edit-form-container p {
            font-size: 16px;
            line-height: 1.6;
        }
        #delete-confirmation {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            width: 300px;
            text-align: center;
        }
        #delete-confirmation p {
            margin-bottom: 15px;
        }
        #delete-confirmation button {
            margin: 5px;
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

        .middle {
            display: flex;
            justify-content: center;
            align-items: center;
        }


        
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="main-content">
            <h1>Menaxhimi i Studentëve</h1>
            <div class="student-actions">
                <button onclick="toggleForm()">Shto Student</button>
            </div>
            <div class="form-container" id="form-container">
                <form id="studentForm" method="POST" action="">
                    <input type="hidden" name="add_student" value="1">
                    <input type="text" placeholder="ID" name="id" required>
                    <input type="text" placeholder="Emri dhe Mbiemri" name="name" required>
                    <input type="text" placeholder="Emri i Prindit" name="parent_name" required>
                    <input type="date" placeholder="Ditëlindja" name="birth_date" required>
                    <input type="text" placeholder="Numri Amë" name="personal_number" required>
                    <input type="text" placeholder="Vendi dhe Komuna" name="place" required>
                    <input type="text" placeholder="Shteti" name="state" required>
                    <input type="text" placeholder="Nënshtetësia" name="citizenship" required>
                    <input type="text" placeholder="Viti Akademik" name="academic_year" required>
                    <label for="study_program">Programi Studimor:</label>
                    <select name="study_program" id="study_program" required>
                        <option value="Informatikë">Informatikë</option>
                        <option value="Informatikë Mësimdhënie">Informatikë Mësimdhënie</option>
                        <option value="Programim i Aplikuar ">Programim i Aplikuar</option>
                        <option value="Shkenca Kompjuterike dhe Inxhinieria">Shkenca Kompjuterike dhe Inxhinieria</option>
                    </select>
                    <label for="study_cycle">Cikli i Studimeve:</label>
                    <select name="study_cycle" id="study_cycle" required>
                        <option value="Parë">Parë</option>
                        <option value="Dytë">Dytë</option>
                    </select>

                    <label for="semester">Semestri:</label>
                    <select name="semester" id="semester" required>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                        <option value="VII">VII</option>
                        <option value="VIII">VIII</option>
                        <option value="Absolvent">Absolvent</option>
                    </select>
                    <button type="submit">Ruaj Studentin</button>
                </form>
            </div>
            
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Kërko sipas ID-së..." onkeyup="filterStudents()">
            </div>

            <div class="saved-students">
                <h2>Studentët e Ruajtur</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Emri dhe Mbiemri</th>
                            <th>Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['id']); ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td>
                                    <button onclick="showStudentDetails(<?php echo htmlspecialchars(json_encode($student)); ?>)">Shiko Detajet</button>
                                    <button onclick="showEditForm(<?php echo htmlspecialchars(json_encode($student)); ?>)">Ndrysho</button>
                                    <button onclick="showDeleteConfirmation('<?php echo htmlspecialchars($student['id']); ?>')">Fshi</button>
                                    <button onclick="showCourseSelection('<?php echo htmlspecialchars($student['id']); ?>')">Shiko Lendet</button>
               
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
         // Show Course Selection Modal
function showCourseSelection(studentId) {
    document.getElementById('modal-student-id').value = studentId;

    

    // Fetch lendet prej  courses.json
    fetch('courses.json')
        .then(response => response.json())
        .then(courses => {
            const dropdown = document.getElementById('course-dropdown');
            dropdown.innerHTML = ''; // Clear previous options
            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.course_name;
                option.textContent = `${course.course_name} (${course.course_code})`;
                dropdown.appendChild(option);
            });
            document.getElementById('course-selection-modal').style.display = 'block';
        })
        .catch(error => console.error('Error fetching courses:', error));
        
}


// Close Course Selection Modal
function closeCourseModal() {
    document.getElementById('course-selection-modal').style.display = 'none';
}

    </script>


    <!-- qeket formen ekam shtu -->
<div id="course-selection-modal" style="display: none;">
    <div class="student-details active">
        <h2>Zgjidh Lenden</h2>
        <form id="studentForm" method="POST"  action="">
                <input type="hidden" name="student_id" id="modal-student-id">    
                <label for="course-dropdown">Lenda:</label>
                <select name="course_name" id="course-dropdown" required></select>
                    
                <br>
                <label for="grade">Nota:</label>
            <select name="grade" id="grade" required>
                <option value="10/A">10/A</option>
                <option value="9/B">9/B</option>
                <option value="8/C">8/C</option>
                <option value="7/D">7/D</option>
                <option value="6/E">6/E</option>
            </select>
                 <br>
            <button type="submit">Ruaj Zgjedhjen</button>
            <button type="button" onclick="closeCourseModal()">Anulo</button>
        </form>
        
        <br>

    </div>
</div>
<!-- qeket ekam shtu posht -->


    <!-- Formulari për ndryshimin e studentit -->
    <div class="form-container edit-form-container" id="edit-form-container">
        <form id="editStudentForm" method="POST" action="">
            <input type="hidden" name="edit_student" value="1">
            <input type="hidden" name="student_id" id="edit-student-id">
            <input type="text" placeholder="ID" name="id" id="edit-id" required>
            <input type="text" placeholder="Emri dhe Mbiemri" name="name" id="edit-name" required>
            <input type="text" placeholder="Emri i Prindit" name="parent_name" id="edit-parent_name" required>
            <input type="date" placeholder="Ditëlindja" name="birth_date" id="edit-birth_date" required>
            <input type="text" placeholder="Numri Amë" name="personal_number" id="edit-personal_number" required>
            <input type="text" placeholder="Vendi dhe Komuna" name="place" id="edit-place" required>
            <input type="text" placeholder="Shteti" name="state" id="edit-state" required>
            <input type="text" placeholder="Nënshtetësia" name="citizenship" id="edit-citizenship" required>
            <input type="text" placeholder="Viti Akademik" name="academic_year" id="edit-academic_year" required>
            <label for="study_program">Programi Studimor:</label>
            <select name="study_program" id="edit-study_program" required>
                <option value="Informatikë">Informatikë</option>
                <option value="Informatikë Mësimdhënie">Informatikë Mësimdhënie</option>
                <option value="Programim i Aplikuar ">Programim i Aplikuar</option>
                <option value="Shkenca Kompjuterike dhe Inxhinieria">Shkenca Kompjuterike dhe Inxhinieria</option>
            </select>
            <label for="study_cycle">Cikli i Studimeve:</label>
            <select name="study_cycle" id="edit-study_cycle" required>
                <option value="Parë">Parë</option>
                <option value="Dytë">Dytë</option>
            </select>
            <label for="semester">Semestri:</label>
            <select name="semester" id="edit-semester" required>
                <option value="I">I</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
                <option value="V">V</option>
                <option value="VI">VI</option>
                <option value="VII">VII</option>
                <option value="VIII">VIII</option>
                <option value="Absolvent">Absolvent</option>
            </select>
            <button type="submit">Përditëso Studentin</button>
            <button type="button" onclick="closeEditForm()">Anulo</button>
        </form>
    </div>


    <!-- Konfirmimi i fshirjes -->
    <div id="delete-confirmation">
        <p>Jeni të sigurt që dëshironi të fshini këtë student?</p>
        <form id="deleteForm" method="POST" action="">
            <input type="hidden" name="delete_student" value="1">
            <input type="hidden" name="student_id" id="delete-student-id">
            <button type="submit">Po</button>
            <button type="button" onclick="closeDeleteConfirmation()">Jo</button>
        </form>
    </div>

    <script>
        
        // Funksioni për të hapur formularin për shtimin e studentëve
        function toggleForm() {
            var formContainer = document.getElementById('form-container');
            formContainer.classList.toggle('active');
        }

        // Funksioni për të hapur formularin për ndryshimin e studentëve
        function showEditForm(student) {
            var editFormContainer = document.getElementById('edit-form-container');
            var form = document.getElementById('editStudentForm');

            document.getElementById('edit-student-id').value = student.id;
            document.getElementById('edit-id').value = student.id;
            document.getElementById('edit-name').value = student.name;
            document.getElementById('edit-parent_name').value = student.parent_name;
            document.getElementById('edit-birth_date').value = student.birth_date;
            document.getElementById('edit-personal_number').value = student.personal_number;
            document.getElementById('edit-place').value = student.place;
            document.getElementById('edit-state').value = student.state;
            document.getElementById('edit-citizenship').value = student.citizenship;
            document.getElementById('edit-academic_year').value = student.academic_year;
            document.getElementById('edit-study_program').value = student.study_program;
            document.getElementById('edit-study_cycle').value = student.study_cycle;
            document.getElementById('edit-semester').value = student.semester;

            editFormContainer.classList.add('active');
        }

        // Funksioni për të mbyllur formularin për ndryshimin e studentëve
        function closeEditForm() {
            var editFormContainer = document.getElementById('edit-form-container');
            editFormContainer.classList.remove('active');
        }
        
        
        // Funksioni për të hapur detajet e studentit
function showStudentDetails(student) {
    // Fetch courses.json to merge static course data
    fetch('courses.json')
        .then(response => response.json())
        .then(coursesJson => {
            // Create the details container
            var studentDetails = document.createElement('div');
            studentDetails.classList.add('student-details');
            studentDetails.classList.add('active');

            studentDetails.innerHTML = `
                <div class="university-name">Universiteti "Nënë Tereza" - Shkup</div>
                <div class="faculty-name">Fakulteti i Shkencave të Informatikës</div>
                <p><strong>ID:</strong> ${student.id}</p>
                <p><strong>Emri dhe Mbiemri:</strong> ${student.name}</p>
                <p><strong>Emri i Prindit:</strong> ${student.parent_name}</p>
                <p><strong>Ditëlindja:</strong> ${student.birth_date}</p>
                <p><strong>Numri Amë:</strong> ${student.personal_number}</p>
                <p><strong>Vendi dhe Komuna:</strong> ${student.place}</p>
                <p><strong>Shteti:</strong> ${student.state}</p>
                <p><strong>Nënshtetësia:</strong> ${student.citizenship}</p>
                <p><strong>Viti Akademik:</strong> ${student.academic_year}</p>
                <p><strong>Programi Studimor:</strong> ${student.study_program}</p>
                <p><strong>Cikli i Studimeve:</strong> ${student.study_cycle}</p>
                <p><strong>Semestri:</strong> ${student.semester}</p>
                <hr>
                <h3 class="middle"><u>Lëndët e Kaluara:</u></h3>
                <div class="registered-courses" >
                <table id="course-table" >
                    <thead>
                        <tr>
                            <th>Emri i Lëndës</th>
                            <th>Fondi i Orëve</th>
                            <th>Statusi</th>
                            <th>ECTS</th>
                            <th>Nota</th>
                            <th>Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be populated dynamically -->
                    </tbody>
                </table>
                </div>
                <button onclick="closeStudentDetails()">Mbyll</button>
            `;

            
            // Populate the course table
            const tableBody = studentDetails.querySelector('#course-table tbody');

            if (student.courses && student.courses.length > 0) {
                student.courses.forEach(studentCourse => {
                    const courseData = coursesJson.find(c => c.course_name === studentCourse.selected_course);

                    if (courseData) {
                        // Create table row
                        const row = document.createElement('tr');

                        // Populate cells with data
                        const courseNameCell = document.createElement('td');
                        courseNameCell.textContent = courseData.course_name;

                        const courseHoursCell = document.createElement('td');
                        courseHoursCell.textContent = courseData.course_hours;

                        const courseStatusCell = document.createElement('td');
                        courseStatusCell.textContent = courseData.course_status;

                        const ectsCell = document.createElement('td');
                        ectsCell.textContent = courseData.ects;

                        const gradeCell = document.createElement('td');
                        gradeCell.textContent = studentCourse.grade; // Use grade from student data

                         // Create delete button
                         const actionsCell = document.createElement('td');
                        const deleteButton = document.createElement('button');
                        deleteButton.textContent = 'Fshi';
                        deleteButton.classList.add('delete-button');
                        deleteButton.onclick = () => {
                            if (confirm('A jeni të sigurt që dëshironi të fshini këtë rresht?')) {
                                row.remove();
                            }
                        };
                        actionsCell.appendChild(deleteButton);

                        // Append cells to row
                        row.appendChild(courseNameCell);
                        row.appendChild(courseHoursCell);
                        row.appendChild(courseStatusCell);
                        row.appendChild(ectsCell);
                        row.appendChild(gradeCell);
                         row.appendChild(actionsCell);

                        // Append row to table body
                        tableBody.appendChild(row);
                    }
                });
            } else {
                // No courses available
                const noDataRow = document.createElement('tr');
                const noDataCell = document.createElement('td');
                noDataCell.colSpan = 5;
                noDataCell.textContent = 'Nuk ka të dhëna për lëndët.';
                noDataRow.appendChild(noDataCell);
                tableBody.appendChild(noDataRow);
            }

            // Append the details to the body
            document.body.appendChild(studentDetails);
        })
        .catch(error => console.error('Error fetching courses:', error));
}


// Funksioni për të mbyllur detajet e studentit
function closeStudentDetails() {
    const studentDetails = document.querySelector('.student-details.active');
    if (studentDetails) {
        studentDetails.remove();
    }
}



        
        //Search ID
        function filterStudents() {
            var input, filter, table, rows, cells, idValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.querySelector(".saved-students table");
            rows = table.getElementsByTagName("tr");

            // Loop në rreshtat e tabelës (përveç header-it)
            for (var i = 1; i < rows.length; i++) {
                cells = rows[i].getElementsByTagName("td");
                if (cells.length > 0) {
                    idValue = cells[0].textContent || cells[0].innerText;
                    if (idValue.toUpperCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }


        // Funksioni për të mbyllur detajet e studentit
        function closeStudentDetails() {
            var studentDetails = document.querySelector('.student-details');
            studentDetails.remove();
        }

        // Funksioni për të hapur konfirmimin e fshirjes
        function showDeleteConfirmation(studentId) {
            document.getElementById('delete-student-id').value = studentId;
            var deleteConfirmation = document.getElementById('delete-confirmation');
            deleteConfirmation.style.display = 'block';
        }

        // Funksioni për të mbyllur konfirmimin e fshirjes
        function closeDeleteConfirmation() {
            var deleteConfirmation = document.getElementById('delete-confirmation');
            deleteConfirmation.style.display = 'none';
        }

         
    </script>

   
</body>
</html>
