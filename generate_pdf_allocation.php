<?php
require 'fpdf186/fpdf.php'; // Ensure the path to fpdf.php is correct
include 'db_connect.php';

// Fetch selected month and year from POST data
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Convert month number to month name
$monthName = date("F", mktime(0, 0, 0, $selectedMonth, 10));

// Fetch GST percentages from database
$gst_query = "SELECT cgst_percentage, sgst_percentage FROM gst LIMIT 1";
$gst_result = $conn->query($gst_query);
if (!$gst_result) {
    die("Failed to fetch GST percentages: " . $conn->error);
}
$gst_data = $gst_result->fetch_assoc();
$cgst_percentage = $gst_data['cgst_percentage'];
$sgst_percentage = $gst_data['sgst_percentage'];

class PDF extends FPDF
{
    private $unitTotal; // Variable to hold unit total
    private $monthName; // Variable to hold month name
    private $year;      // Variable to hold year

    // Constructor to initialize month and year
    function __construct($monthName, $year)
    {
        parent::__construct();
        $this->monthName = $monthName;
        $this->year = $year;
    }

    // Page header
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial', 'B', 12);
        // Title
        $this->Cell(0, 5, 'East Coast Railway', 0, 1, 'C');
        $this->Cell(0, 5, 'Bhubaneswar', 0, 1, 'C');
        $this->Cell(0, 5, 'Consolidated CUG Bill', 0, 1, 'C');
        $this->Cell(0, 5, 'For the Month of: ' . $this->monthName . ' ' . $this->year, 0, 1, 'C'); // Add month and year
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
    function LoadData($conn, $selectedMonth, $selectedYear)
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
            WHERE 
                b.bill_month = '$selectedMonth' AND b.bill_year = '$selectedYear'
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

    // Generate table with headers having top and bottom borders
    function BasicTable($header, $data)
    {
        // Header with borders
        $this->SetFont('Arial', 'B', 10);
        foreach ($header as $col) {
            // Add border to top and bottom of each header cell
            $this->Cell(60, 7, $col, 'TB', 0, 'C');
        }
        $this->Ln();

        // Data rows without borders
        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            $this->Cell(60, 5, $row['allocation'], 0, 0, 'C');
            $this->Cell(60, 5, $row['bill_dates'], 0, 0, 'C');
            $this->Cell(60, 5, 'Rs. ' . number_format($row['total_amount'], 2), 0, 1, 'C');
        }
    }

    // Calculate total payable amount including CGST and SGST using fetched percentages
    function CalculateTotalPayable($data, $cgst_percentage, $sgst_percentage)
    {
        $total_amount = array_sum(array_column($data, 'total_amount'));
        $cgst = ($total_amount * $cgst_percentage) / 100;
        $sgst = ($total_amount * $sgst_percentage) / 100;
        $total_payable = $total_amount + $cgst + $sgst;

        return [
            'total_amount' => $total_amount,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'total_payable' => $total_payable
        ];
    }

    // Render total amounts
    function RenderTotals($totals)
    {
        $this->Ln(3); // Line break before totals
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(180, 0, '', 'T', 1); // Line
        $this->Cell(120, 6, 'Grand Total:', 0, 0, 'R');
        $this->Cell(60, 6, 'Rs. ' . number_format($totals['total_amount'], 2), 0, 1, 'R');

        $this->Cell(120, 6, 'CGST Rs.:', 0, 0, 'R');
        $this->Cell(60, 6, 'Rs. ' . number_format($totals['cgst'], 2), 0, 1, 'R');

        $this->Cell(120, 6, 'SGST Rs.:', 0, 0, 'R');
        $this->Cell(60, 6, 'Rs. ' . number_format($totals['sgst'], 2), 0, 1, 'R');

        $pageWidth = $this->getPageWidth(); // Get the width of the page
        $lineLength = 100; // Length of the line in millimeters
        $lineHeight = 3; // Height of the line in millimeters
        $linePositionX = $pageWidth - $lineLength - 10; // X position to start the line (adjust as needed)
        $this->SetLineWidth(0.8); // Set line width to 0.8mm (adjust as needed)
        $this->Line($linePositionX, $this->GetY(), $linePositionX + $lineLength, $this->GetY()); // Draw a line

        $this->Cell(120, 10, 'Total Payable:', 0, 0, 'R');
        $this->Cell(60, 10, 'Rs. ' . number_format($totals['total_payable'], 2), 0, 1, 'R');
    }

    // Render summary text
    function RenderSummary($totals)
    {
        $this->Ln(10); // Line break before summary
        $this->SetFont('Arial', '', 10);
        $lineheight = 6; // Adjust line height as needed
        $this->MultiCell(0, $lineheight, "Passed for Rs. " . number_format($totals['total_payable'], 2) . " (Rupees " . convert_number_to_words($totals['total_payable']) . " Only) and forwarded to FA & CAO IX/BBS for audit and arranging the payment of net amount of Rs. " . number_format($totals['total_payable'], 2) . " (Rupees " . convert_number_to_words($totals['total_payable']) . " Only)");
    }

    // Render signature space
    function RenderSignature()
    {
        $this->Ln(20); // Line break before signature
        $this->SetFont('Arial', '', 10);
        $lineheight = 6; // Adjust line height as needed
        $this->Cell(0, $lineheight, 'For PCSTE/ECOR', 0, 1, 'R');
        $this->Cell(0, $lineheight, 'ECo Rly, Bhubaneswar', 0, 1, 'R');
    }
}

$pdf = new PDF($monthName, $selectedYear);
$pdf->SetTitle("Allocation Wise Statement for $monthName $selectedYear");
// Column headings
$header = ['Allocation', 'Bill Dates', 'Amount (Rs.)'];
// Data loading
$data = $pdf->LoadData($conn, $selectedMonth, $selectedYear);

$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->BasicTable($header, $data);

// Calculate total payable amounts using fetched GST percentages
$totals = $pdf->CalculateTotalPayable($data, $cgst_percentage, $sgst_percentage);

// Render totals and summary
$pdf->RenderTotals($totals);
$pdf->RenderSummary($totals);
$pdf->RenderSignature();

// Output PDF to browser
$pdf->Output('D', "Allocation Wise Statement for $monthName $selectedYear.pdf");

// Close database connection
$conn->close();

// Function to convert number to words (Indian numbering system)
function convert_number_to_words($number)
{
    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = array(
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
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
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
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
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}
?>