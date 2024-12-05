<?php
// Connect to database
require 'connectDB.php';
require 'vendor/autoload.php'; // Pastikan autoload.php disertakan

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$output = '';

if (isset($_POST["To_Excel"])) {

    $Start_date = "";
    $End_date = "";
    $Start_time = "";
    $End_time = "";
    $dev_dep = "";

    // Start building search query
    $_SESSION['searchQuery'] = "1=1"; // Default condition for AND concatenation later

    // Date filter
    if ($_POST['date_sel'] == "Date_in") {
        // Start date filter
        if (!empty($_POST['date_sel_start'])) {
            $Start_date = $_POST['date_sel_start'];
            $_SESSION['searchQuery'] .= " AND checkoutdate1 >= '".$Start_date."'";
        } else {
            $Start_date = date("Y-m-d");
            $_SESSION['searchQuery'] .= " AND checkoutdate1 >= '".date("Y-m-d")."'";
        }

        // End date filter
        if (!empty($_POST['date_sel_end'])) {
            $End_date = $_POST['date_sel_end'];
            $_SESSION['searchQuery'] .= " AND checkoutdate1 <= '".$End_date."'";
        }
    } elseif ($_POST['date_sel'] == "Date_out") {
        // Start date filter
        if (!empty($_POST['date_sel_start'])) {
            $Start_date = $_POST['date_sel_start'];
            $_SESSION['searchQuery'] .= " AND checkindate1 >= '".$Start_date."'";
        }

        // End date filter
        if (!empty($_POST['date_sel_end'])) {
            $End_date = $_POST['date_sel_end'];
            $_SESSION['searchQuery'] .= " AND checkindate1 <= '".$End_date."'";
        }
    }

    // Time filter
    if ($_POST['time_sel'] == "Time_in") {
        // Start time filter
        if (!empty($_POST['time_sel_start'])) {
            $Start_time = $_POST['time_sel_start'];
            $_SESSION['searchQuery'] .= " AND timeout1 >= '".$Start_time."'";
        }

        // End time filter
        if (!empty($_POST['time_sel_end'])) {
            $End_time = $_POST['time_sel_end'];
            $_SESSION['searchQuery'] .= " AND timeout1 <= '".$End_time."'";
        }
    } elseif ($_POST['time_sel'] == "Time_out") {
        // Start time filter
        if (!empty($_POST['time_sel_start'])) {
            $Start_time = $_POST['time_sel_start'];
            $_SESSION['searchQuery'] .= " AND timein1 >= '".$Start_time."'";
        }

        // End time filter
        if (!empty($_POST['time_sel_end'])) {
            $End_time = $_POST['time_sel_end'];
            $_SESSION['searchQuery'] .= " AND timein1 <= '".$End_time."'";
        }
    }

    // Department filter
    if (!empty($_POST['dev_sel'])) {
        $dev_dep = $_POST['dev_sel'];
        $_SESSION['searchQuery'] .= " AND device_dep='".$dev_dep."'";
    }

    // SQL query to select filtered data and order by department
    $sql = "SELECT * FROM users_logs WHERE ".$_SESSION['searchQuery']." ORDER BY device_dep ASC";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        // Buat objek spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header untuk Excel
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Card UID');
        $sheet->setCellValue('D1', 'Department');
        $sheet->setCellValue('E1', 'Class');
        $sheet->setCellValue('F1', 'Room');
        $sheet->setCellValue('G1', 'Date In');
        $sheet->setCellValue('H1', 'Date Out');
        $sheet->setCellValue('I1', 'Time In');
        $sheet->setCellValue('J1', 'Time Out');

        // Mula masukkan data dari result ke dalam Excel
        $row = 2; // Mula pada baris kedua
        while ($row_data = $result->fetch_assoc()) {
            $sheet->setCellValue('A'.$row, $row_data['id']);
            $sheet->setCellValue('B'.$row, $row_data['username']);
            $sheet->setCellValue('C'.$row, $row_data['card_uid']);
            $sheet->setCellValue('D'.$row, $row_data['device_dep']);
            $sheet->setCellValue('E'.$row, $row_data['class']);
            $sheet->setCellValue('F'.$row, $row_data['no_room']);
            $sheet->setCellValue('G'.$row, $row_data['checkindate1']);
            $sheet->setCellValue('H'.$row, $row_data['checkoutdate1']);
            $sheet->setCellValue('I'.$row, $row_data['timein1']);
            $sheet->setCellValue('J'.$row, $row_data['timeout1']);
            $row++;
        }

        // Simpan ke fail Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'User_Log_' . $Start_date . '.xlsx';

        // Hantar fail ke pelayar untuk dimuat turun
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    } else {
        header("location: UsersLog.php");
        exit();
    }
}
?>
