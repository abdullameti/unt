
<?php 
// Funksioni për të marrë të dhënat e studentit nga skedari JSON
function getStudentData($studentId) {
    // Lexo të dhënat nga skedari students.json
    $jsonData = file_get_contents('students.json');

    // Kontrollo nëse ka një problem gjatë leximit të skedarit
    if ($jsonData === false) {
        die("Gabim në leximin e skedarit.");
    }

    // Dekodimi i të dhënave të skedarit në array
    $students = json_decode($jsonData, true);

    // Kontrollo nëse ka një problem në dekodimin e JSON-it
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Gabim në dekodimin e JSON: " . json_last_error_msg());
    }

    // Kërko studentin në listë bazuar në ID
    foreach ($students as $student) {
        if ($student['id'] === $studentId) {
            return $student; // Kthe të dhënat e studentit nëse gjendet
        }
    }

    // Kthe null nëse studenti nuk gjendet
    return null;
}

// Inicializo të dhënat e studentit si null
$studentData = null;


// Kontrollo nëse kërkesa është POST dhe ID e studentit është dërguar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['studentId'])) {
    // Sanitizo inputin për siguri
    $studentId = htmlspecialchars(trim($_POST['studentId']));

    // Kërko të dhënat e studentit bazuar në ID
    $studentData = getStudentData($studentId);
}


function getCourseData() {
    $jsonData = file_get_contents('courses.json');
    if ($jsonData === false) {
        die("Gabim në leximin e skedarit.");
    }

    $courses = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Gabim në dekodimin e JSON: " . json_last_error_msg());
    }

    return $courses;
}

function gradeToText($grade) {
    $grades = [
        "6/E" => "Gjashtë",
        "7/D" => "Shtatë",
        "8/C" => "Tetë",
        "9/B"=> "Nëntë",
        "10/A"=> "Dhjetë"
    ];
    return $grades[$grade] ;
}

$studentData = null;
$courses = getCourseData();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['studentId'])) {
    $studentId = htmlspecialchars(trim($_POST['studentId']));
    $studentData = getStudentData($studentId);
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=794px, initial-scale=1">
    <title>Krijo Çertifikatë</title>
    <style>
        * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}


.body {
  display: grid;
  place-self: center;
  margin: 0;
  padding: 0;
  width: 794px;
  height: 1123px;
  font-family: Arial, sans-serif;
  font-size: 14px;
}

.certificate {
  width: 100%;
  height: 100%;
  padding: 40px;
  border: 1px solid #ccc;
}

.header {
  display: grid;
  align-items: center;
  margin-bottom: 20px;
}

.flexheader {
  display: flex;
  justify-content: space-between;
  align-items: end;
}

.center {
  font-family: "Times New Roman", Times, serif;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: normal;
}

header img {
  display: block;
  margin: auto;
}

header p {
  font-size: 12px;
  font-weight: bold;
  line-height: 1.4;
}

hr {
  border: none;
  border-top: 2px solid #000;
  margin: 10px 0;
}

h4 {
  text-align: center;
  margin: 20px 0;
  font-size: 20px;
  line-height: 1.4;
  font-style: italic;
  font-family: Tahoma;
}

.certificate-body table {
  width: 100%;
  border-collapse: collapse;
  margin: 10px 0;
  font-size: 12px;
}

.certificate-body td:nth-child(1) {
  font-weight: bold;
  font-size: 12px;
}

.certificate-body table td {
  width: 40%;
  border: 1px solid #ccc;
  padding: 5px;
  vertical-align: top;
  font-size: 12px;
}

.note {
  text-align: center;
  font-style: italic;
  font-size: 12px;
  padding: 10px;
  background: #f1f1f1;
  border: none;
  font-weight: bolder;
}

.flexheader {
  display: flex;
  justify-content: space-between;
}

.exam-table table {
  width: 100%;
  border-collapse: collapse;
  font-size: 10px;
}

.exam-table th,
.exam-table td {
  border: 1px solid #ccc;

  text-align: center;
}
.exam-table tbody td .exam-table thead {
  background-color: #f2f2f2;
  font-weight: bold;
}

.el {
  width: 25%;
}

.end {
  display: flex;
  flex-direction: column;
  font-size: 12px;
}

.end p {
  line-height: 1.5;
}

.leftright {
  display: flex;
  justify-content: space-between;
}

@media print {
  html,
  body {
    width: 210mm;
    height: 297mm;
    margin: 0;
  }

  .certificate {
    padding: 20mm;
    border: none;
  }

  table {
    font-size: 10px;
  }

  @media screen and (max-width: 1024px) {
    html,
    body {
      zoom: 75%; /* Redukto pamjen për ekranet më të vogla */
    }
  }
}

@media print {
    /* Regular page styles */
    .no-print {
            display: none;
        }

        /* Hide other content and only show the certificate during printing */
        @media print {
            body * {
                visibility: hidden;
            }
            .certificate, .certificate * {
                visibility: visible;
            }
            .certificate {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
        /* Shtimi i mundësisë për të rregulluar madhësinë e faqes për printim */
    @page {
        size: A4; /* Mund ta rregullosh në formatin që dëshiron (A4, letter, etj.) */
         /* Heqja e margjinave për më shumë hapësirë */
    }

}

#course-table td , #course-table th {
    padding: 5px;
    font-size: 12px;
}

.sidebar {
    height: 160%;
}


    </style>
</head>
<body>
    <h1>Krijo Çertifikatë</h1>
    
    <form method="POST">
    <input style=" padding:5px; border-radius: 5px" type="text" name="studentId" placeholder="Fut ID e Studentit" required>
    <button style=" padding:5px; border-radius: 5px" type="submit">Kërko</button>
    <button style="padding: 5px; border-radius: 5px; position: relative; float: right;" onclick="printPDF()">PRINT</button>

</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['studentId'])) {
    // Simulate fetching student data based on the ID
    //$studentData = fetchStudentData($_POST['studentId']); // Replace with your actual data retrieval logic

    if ($studentData) {
        // If student data is found, display it
        echo '<p>ID e Kërkuar: ' . htmlspecialchars($_POST['studentId']) . '</p>';
        // You can display more student data here
    } else {
        // If no student data is found, display a message
        echo '<p style="  color: red">Studenti me këtë ID nuk u gjet. Ju lutemi provoni përsëri.</p>';
    }
} 
// If no POST request or studentId is empty, nothing will be shown
?>



    <hr>
    <div class="body">
    <div class="certificate">
      <header class="header">
        <div>
          <div class="snmk">
            <div class="center">
              <img src="IMG/snmk.png" alt="snmk" width="40">
            </div>
            <p class="center">Република Северна Македонија <br>
              Republika e Maqedonisë së veriut</p>
          </div>
          <div class="flexheader">
            <div class="header-left">
                <img
                  src="IMG/logo.png"
                  width="80"
                  alt="University Logo"
                  class="logo"
                />
              </div>
              <div class="header-right">
                <p>Број на досие/Numri i dosjes: <u id="id"><?php echo htmlspecialchars(isset($studentData['id']) ? $studentData['id'] : ' ') . "<br>";?></u></p>
                <p>Дел.број/Nr.pr: 0807- <b>________ /1</b></p>
              </div>
        </div>
        <hr>
        </div>
        
        <P><p>
          ВРЗ ОСНОВА НА ЧЛЕН 155 СТАВОВИ 6, 7 И 8 ОД ЗАКОНОТ ЗА ВИСОКОТО ОБРАЗОВАНИЕ („СЛУЖБЕН ВЕСНИК НА
          РМ“ БРОЈ. 82/18) И ЧЛЕН 293 ОД СТАТУТОТ НА УНИВЕРЗИТЕТОТ „МАЈКА ТЕРЕЗА“ ВО СКОПЈЕ, ИНФОРМАТИЧКИ НАУКИ 
          ФАКУЛТЕТ ПРИ УНИВЕРЗИТЕТОТ ГО ИЗДАВА СЛЕДНОТО: <br>
          NË BAZË TË NENIT 155 PARAGRAFET 6, 7 DHE 8 TË LIGJIT PËR ARSIM TË LARTË ("GAZETA ZYRTARE E RM-së" NR.
          82/18) DHE NENIT 293 TË STATUTIT TË UNIVERSITETIT “NËNË TEREZA” NË SHKUP, FAKULTETI I SHKENCAVE TË INFORMATIKËS LËSHON
          KËTË:
          </p></P>
          
      </header>
      <hr>
      <h4>
        Уверение за положени испити од прв циклус <br>
        Vërtetim i provimeve të dhëna në ciklin e parë
      </h4>
     
      <section class="certificate-body">
        <table>
          <tr>
            <td ><b>Студиска програма</b><br />Programi studimor</td>
            <td colspan="3"  id="study_program"><?php echo htmlspecialchars(isset($studentData['study_program']) ? $studentData['study_program'] : ' ') . "<br>";  ?></td>
          </tr>

          <tr>
            <td><b>Насока/оддел/модул</b><br />Drejtimi/moduli</td>
            <td colspan="3" id="study_program"><?php echo htmlspecialchars(isset($studentData['study_program']) ? $studentData['study_program'] : ' ') . "<br>";  ?></td>
          </tr>

          <tr>
            <td>
              <b>Име, средно име и презиме на студентот</b><br />Emri, emri i
              mesëm dhe mbiemri i studentit
            </td>
            <td colspan="3" id="name"> <?php echo htmlspecialchars(isset($studentData['name']) ? $studentData['name'] : ' ') . "<br>"; ?> </td>
          </tr>

          <tr>
            <td><b>Матичен број</b><br />Numri amë</td>
            <td colspan="3" id="personal_number"><?php echo htmlspecialchars(isset($studentData['personal_number']) ? $studentData['personal_number'] : ' ') . "<br>"; ?></td>
          </tr>

          <tr >
            <td>
              <b>Датум, место, општина и држава на раѓање</b><br />Data, vend,
              komuna dhe shteti i lindjes
            </td>
            <td id="birthdate" style="width:20%;"> <?php echo htmlspecialchars(isset($studentData['birth_date']) ? $studentData['birth_date'] : ' ') . "<br>"; ?> </td>
            <td id="place" style="width:20%;"> <?php echo htmlspecialchars(isset($studentData['place']) ? $studentData['place'] : ' ') . "<br>"; ?></td>
            <td id="state" style="width:20%;"><?php echo htmlspecialchars(isset($studentData['state']) ? $studentData['state'] : ' ') . "<br>"; ?></td>
          </tr>

          <tr>
            <td><b>Државјанство</b><br />Shtetësia</td>
            <td  colspan="3" id="citizenship"> <?php echo htmlspecialchars(isset($studentData['citizenship']) ? $studentData['citizenship'] : ' ') . "<br>"; ?></td>
          </tr>

          <tr>
            <td>
              <b>Година на запишување на студиите</b><br />Viti i regjistrimit
              të studimeve
            </td>
            <td colspan="3" id="academic_year"><?php echo htmlspecialchars(isset($studentData['academic_year']) ? $studentData['academic_year'] : ' ') . "<br>";?></td>
          </tr>
          
          
        </table>
        <br>
      </section>
      <section class="exam-table">
        <tr>
            <td colspan="4" class="note" >
                во текот на студиите , студентот ги положи следниве ицпити: <br>
                Gjatë studimeve, studenti i kaloi provimet në vijim:
            </td>
          </tr>
          <table id="course-table">
                 <thead>
                    <tr>
                        <th>Реден<br>Број</th>
                        <th>Код</th>
                        <th class="el">Назив на предметот</th>
                        <th>Фонд<br>на часови</th>
                        <th>Оценка<br>со број</th>
                        <th>Оценка <br> (опосно)</th>
                        <th>EKTC <br> кредити</th>
                        <th> Статус на предметот 
                        <br>(з/и/м)</th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>Numri<br>Rendor</th>
                        <th>Kodi</th>
                        <th class="el">Emri i lëndës</th>
                        <th>Fondi<br>i orëve</th>
                        <th>Nota<br>me numër</th>
                        <th>Nota <br>  (e shkruar)</th>
                        <th>ECTS <br>kredi</th>
                        <th>Statusi i lëndës <br> (o/z)</th>
                    </tr>
                </thead> 
                <tbody id="bodycourses">
                   
                
                <?php 
            $studentData['courses'] = $studentData['courses'] ?? []; 
            foreach ($studentData['courses'] as $index => $passedCourse): 
                // Gjej të dhënat e kursit bazuar në emrin e kursit
                $courseDetails = null;
                foreach ($courses as $course) {
                    if (trim($course['course_name']) === trim($passedCourse['selected_course'])) {
                        $courseDetails = $course;
                        break;
                    }
                }
            ?>

                
                <tr class="lendet" >
                    <td class="rreshti" ><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($course['course_code']) ?></td>
                    <td><?= htmlspecialchars($passedCourse['selected_course'] ?? '') ?></td>
                    <td><?= htmlspecialchars($course['course_hours'] ?? '') ?></td>
                    <td><?= htmlspecialchars($passedCourse['grade']) ?></td>
                    <td><?= htmlspecialchars(gradeToText($passedCourse['grade'])) ?></td>
                    <td><?= htmlspecialchars($course['ects'] ?? '') ?></td>
                    <td><?= htmlspecialchars($course['course_status'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
      </section>




                </tbody>
            </table>
      </section>

      <script>
       
      </script>

      <br><br><br>
      <section class="end">
        <article>
          <p style="font-weight: bolder;">Делумни реализирана студиска програма/Realizim i pjeserishem i programit studimor:</p>
        <p>Студентот положил <span>____</span>oд вкупно <span>____</span>испити предвидени со студиската програма (вкупен број на испити на
          студиската програма) и се стекнал со <span>____</span>EKTC (кредити).</p>
          <p>Studenti ka kaluar <span>____</span>prej gjithsej <span>____</span>provimeve të parapara me programin studimor (numri i përgjithshëm i
            provimeve në programin studimor) dhe ka akumuluar gjithsej <span>____</span>ECTS (kredi).
            </p>
        </article>
        <br><br><br>
        <div class="leftright">
          <div  class="divleft">
            <p>Декан / Dekani</p>
            <p>____________</p>
          </div>
          <div class="divright">
          <p>Датум / Дата</p>
          <span id="date"></span>
          </div>
        </div>
        <br><br><br><br>
        <div class="center">
          V.V / М.П
        </div>
        <br><br><br>
      </section>
    </div>
    </div>

    
    <script>
      // Function to update the date
      function updateDate() {
        const dateElement = document.getElementById('date');
        const today = new Date();
        
        // Format: DD/MM/YYYY
        const formattedDate = `${today.getDate()}/${today.getMonth() + 1}/${today.getFullYear()}`;
        
        // Set the text content of the span
        dateElement.textContent = formattedDate;
      }
    
      // Call the function on page load
      updateDate();
    
      // Optionally, you could also set an interval to ensure it updates at midnight
      setInterval(() => {
        updateDate();
      }, 86400000); // 86400000ms = 1 day

      // Function to trigger the print dialog for PDF
      function printPDF() {
            window.print(); // This will open the print dialog
        }
    </script>


</body>
</html>
