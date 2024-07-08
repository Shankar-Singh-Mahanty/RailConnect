<?php
require('fpdf186/fpdf.php'); // Ensure the path to fpdf.php is correct
include 'db_connect.php';

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Title
        $this->Cell(0, 10, 'East Coast Railway, Bhubaneswar', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Consolidated CUG Bill', 0, 1, 'C');
        $this->Ln(10); // Line break
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Load data from database
    function LoadData($conn)
    {
        $query = "
            SELECT 
                c.allocation,
                GROUP_CONCAT(DISTINCT CONCAT(b.bill_month, '-', b.bill_year) ORDER BY b.bill_year, b.bill_month ASC SEPARATOR ', ') AS bill_dates,
                SUM(b.periodic_charge + b.usage_amount + b.data_amount + b.voice + b.video + b.sms + b.vas) AS total_amount
            FROM 
                cugdetails c
            JOIN 
                bills b ON c.cug_number = b.cug_number
            GROUP BY 
                c.allocation
            ORDER BY 
                c.allocation;
        ";

        $result = $conn->query($query);
        if (!$result) {
            die("Query failed: " . $conn->error);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Generate table
    function BasicTable($header, $data)
    {
        // Header
        foreach ($header as $col) {
            $this->Cell(60, 7, $col, 1);
        }
        $this->Ln();
        // Data
        foreach ($data as $row) {
            $this->Cell(60, 6, $row['allocation'], 1);
            $this->Cell(60, 6, $row['bill_dates'], 1);
            $this->Cell(60, 6, 'Rs. ' . number_format($row['total_amount'], 2), 1);
            $this->Ln();
        }
    }

    // Calculate total payable amount including CGST and SGST
    function CalculateTotalPayable($data)
    {
        $total_amount = array_sum(array_column($data, 'total_amount'));
        $cgst = $total_amount * 0.09; // Assuming CGST rate is 9%
        $sgst = $total_amount * 0.09; // Assuming SGST rate is 9%
        $total_payable = $total_amount + $cgst + $sgst;

        return [
            'total_amount' => $total_amount,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'total_payable' => $total_payable
        ];
    }
}

$pdf = new PDF();
// Column headings
$header = ['Allocation', 'Bill Dates', 'Amount'];
// Data loading
$data = $pdf->LoadData($conn);

$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->BasicTable($header, $data);

// Calculate total payable amounts
$totals = $pdf->CalculateTotalPayable($data);

// Output the table with totals
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total Payable Amount: Rs. ' . number_format($totals['total_payable'], 2), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 10, "Passed for Rs. " . number_format($totals['total_payable'], 2) . " (Rupees " . convert_number_to_words($totals['total_payable']) . " Only) and forwarded to FA & CAO IX/BBS for audit and arranging the payment of net amount of Rs. " . number_format($totals['total_payable'], 2) . " (Rupees " . convert_number_to_words($totals['total_payable']) . " Only), including CGST: Rs. " . number_format($totals['cgst'], 2) . " and SGST: Rs. " . number_format($totals['sgst'], 2));

// Output PDF to browser
$pdf->Output('D', 'consolidated_cug_bill.pdf');

// Close database connection
$conn->close();

// Function to convert number to words (Indian numbering system)
function convert_number_to_words($number)
{
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'forty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int)($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string)$fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}
?>
