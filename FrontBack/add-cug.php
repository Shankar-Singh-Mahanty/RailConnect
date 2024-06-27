<?php
session_start();

// Include database connection script
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['NAME']);
    $cug_no = trim($_POST['CUG_NO']);
    $emp_no = trim($_POST['EMP_NO']);
    $designation = trim($_POST['DESIGNATION']);
    $unit = trim($_POST['UNIT']);
    $department = trim($_POST['DEPARTMENT']);
    $bill_unit_no = trim($_POST['BILL_UNIT_NO']);
    $allocation = trim($_POST['ALLOCATION']);
    $operator = trim($_POST['OPERATOR']);
    $plan = trim($_POST['PLAN']);
    $status = "Active";

    $errors = [];
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($cug_no) || strlen($cug_no) != 11 || !is_numeric($cug_no)) $errors[] = "CUG No must be an 11-digit number.";
    if (empty($emp_no) || strlen($emp_no) != 12 || !is_numeric($emp_no)) $errors[] = "Employee No must be a 12-digit number.";
    if (empty($designation)) $errors[] = "Designation is required.";
    if ($unit == "default") $errors[] = "Unit is required.";
    if ($department == "default") $errors[] = "Department is required.";
    if (empty($bill_unit_no)) $errors[] = "Bill Unit No is required.";
    if (empty($allocation) || !is_numeric($allocation)) $errors[] = "Allocation must be a numeric value.";
    if ($operator == "default") $errors[] = "Operator is required.";
    if (empty($plan) || !in_array($plan, ['A', 'B', 'C'])) $errors[] = "Invalid plan selected.";

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    } else {
        $stmt = $conn->prepare("INSERT INTO CUGDetails (cug_number, emp_number, empname, designation, unit, department, bill_unit_no, allocation, operator, plan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("iisssssdsss", $cug_no, $emp_no, $name, $designation, $unit, $department, $bill_unit_no, $allocation, $operator, $plan, $status);

        if ($stmt->execute()) {
            $_SESSION['success'] = "CUG is successfully allotted to $name";
        } else {
            $_SESSION['errors'] = ["Error: " . $stmt->error];
        }

        $stmt->close();
    }

    header("Location: add-cug.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add CUG</title>
    <link rel="stylesheet" href="base.css" />
    <link rel="stylesheet" href="add-cug.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: "#eef2ff",
                            100: "#e0e7ff",
                            200: "#c7d2fe",
                            300: "#a5b4fc",
                            400: "#818cf8",
                            500: "#6366f1",
                            600: "#4f46e5",
                            700: "#4338ca",
                            800: "#3730a3",
                            900: "#312e81",
                            950: "#1e1b4b",
                        },
                        "accent-color": "var(--accent-color)",
                        "accent-color-hover": "var(--accent-color-hover)",
                    },
                },
                fontFamily: {
                    body: [
                        "Raleway",
                        "ui-sans-serif",
                        "system-ui",
                        "-apple-system",
                        "system-ui",
                        "Segoe UI",
                        "Roboto",
                        "Helvetica Neue",
                        "Arial",
                        "Noto Sans",
                        "sans-serif",
                        "Apple Color Emoji",
                        "Segoe UI Emoji",
                        "Segoe UI Symbol",
                        "Noto Color Emoji",
                    ],
                    sans: [
                        "Raleway",
                        "ui-sans-serif",
                        "system-ui",
                        "-apple-system",
                        "system-ui",
                        "Segoe UI",
                        "Roboto",
                        "Helvetica Neue",
                        "Arial",
                        "Noto Sans",
                        "sans-serif",
                        "Apple Color Emoji",
                        "Segoe UI Emoji",
                        "Segoe UI Symbol",
                        "Noto Color Emoji",
                    ],
                },
            },
        };
    </script>
</head>
<body>
    <main id="main">
        <h2 class="heading">ALLOTMENT OF NEW CUG</h2>
        <?php if(isset($_SESSION['errors'])): ?>
            <div class="session-message">
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="session-message">
                <p><?php echo $_SESSION['success']; ?></p>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form class="form_container" action="add-cug.php" method="post">
            <div class="input_box long-input">
                <label for="name">Name</label>
                <input
                    class="py-2 px-3"
                    type="text"
                    placeholder="Enter Name"
                    name="NAME"
                    required
                />
            </div>
            <div class="input_box">
                <label for="cugno">CUG No</label>
                <input
                    class="py-2 px-3"
                    type="number"
                    placeholder="Enter CUG no."
                    name="CUG_NO"
                    required
                />
            </div>
            <div class="input_box">
                <label for="empno">Employee No.</label>
                <input
                    class="py-2 px-3"
                    type="number"
                    placeholder="Enter Employee no."
                    name="EMP_NO"
                    required
                />
            </div>
            <div class="input_box">
                <label for="designation">Designation</label>
                <input
                    class="py-2 px-3"
                    type="text"
                    placeholder="Enter Designation"
                    name="DESIGNATION"
                    required
                />
            </div>
            <div class="input_box">
                <label for="unit">Unit</label>
                <select class="py-2 px-2" id="unit" name="UNIT">
                    <option value="default">Select an Option</option>
                    <option value="con">CON</option>
                    <option value="hq">HQ</option>
                    <option value="mcs">MCS</option>
                </select>
            </div>
            <div class="input_box">
                <label for="department">Department</label>
                <select class="py-2 px-2" id="department" name="DEPARTMENT">
                    <option value="default">Select an Option</option>
                    <option value="s&t">S&T</option>
                    <option value="engg">ENGG</option>
                    <option value="accts">ACCTS</option>
                    <option value="elect">ELECT</option>
                    <option value="optg">OPTG</option>
                    <option value="pers">PERS</option>
                    <option value="security">SECURITY</option>
                    <option value="audit">AUDIT</option>
                    <option value="med">MED</option>
                    <option value="comm">COMM</option>
                    <option value="ga">GA</option>
                    <option value="mech">MECH</option>
                    <option value="safety">SAFETY</option>
                    <option value="stores">STORES</option>
                    <option value="rrc">RRC</option>
                    <option value="wagon">WAGON</option>
                    <option value="welfare">WELFARE</option>
                </select>
            </div>
            <div class="input_box">
                <label for="billunitno">Bill Unit No.</label>
                <input
                    class="py-2 px-3"
                    type="text"
                    placeholder="Enter Bill Unit no."
                    name="BILL_UNIT_NO"
                    required
                />
            </div>
            <div class="input_box">
                <label for="allocation">Allocation</label>
                <input
                    class="py-2 px-3"
                    type="number"
                    placeholder="Enter Allocation"
                    name="ALLOCATION"
                    required
                />
            </div>
            
            <div class="input_box">
                <label for="operator">Operator</label>
                <select class="py-2 px-2" id="operator" name="OPERATOR">
                    <option value="default">Select an Option</option>
                    <option value="jio">Jio</option>
                    <option value="airtel">Airtel</option>
                    <option value="vi">VI</option>
                    <option value="bsnl">BSNL</option>
                </select>
            </div>
            <div class="input_box">
                <label for="plan">Plan:</label>
                <input
                    class="py-2 px-3"
                    type="text"
                    id="selectedPlan"
                    name="PLAN"
                    readonly
                    required
                />
            </div>
            <section>
                <div class="py-4 px-4 mx-auto max-w-screen-xl lg:px-6">
                    <div
                        class="mx-auto max-w-screen-md text-center mb-2 lg:mb-6"
                    >
                        <h2
                            class="text-2xl tracking-tight font-extrabold text-white"
                        >
                            Choose a Plan
                        </h2>
                    </div>
                    <div
                        class="space-y-8 lg:grid lg:grid-cols-3 sm:gap-6 xl:gap-10 lg:space-y-0"
                    >
                        <!-- Pricing Card -->
                        <div
                            class="flex flex-col w-full p-6 mx-auto max-w-lg text-center text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white"
                        >
                            <div
                                class="flex justify-center items-baseline my-8"
                            >
                                <span class="mr-2 text-5xl font-extrabold"
                                    >₹74.61</span
                                >
                            </div>
                            <h3 class="mb-4 text-xl font-semibold">
                                Validity: 84days
                            </h3>
                            <p
                                class="font-medium text-sm text-gray-800 sm:text-lg dark:text-gray-400"
                            >
                                Data: 2.0GB/day
                            </p>
                            <p
                                class="font-light text-gray-800 sm:text-lg dark:text-gray-400"
                            >
                                Talktime: Unlimited
                            </p>
                            <button
                                type="button"
                                data-plan="A"
                                class="plan-option text-white bg-accent-color hover:bg-accent-color-hover focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm mt-4 px-5 py-2.5 text-center dark:text-white dark:focus:ring-primary-900"
                            >
                                Select A
                            </button>
                        </div>
                        <!-- Pricing Card -->
                        <div
                            class="flex flex-col w-full p-6 mx-auto max-w-lg text-center text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white"
                        >
                            <div
                                class="flex justify-center items-baseline my-8"
                            >
                                <span class="mr-2 text-5xl font-extrabold"
                                    >₹59.05</span
                                >
                            </div>
                            <h3 class="mb-4 text-xl font-semibold">
                                Validity: 56days
                            </h3>
                            <p
                                class="font-medium text-sm text-gray-800 sm:text-lg dark:text-gray-400"
                            >
                                Data: 1.5GB/day
                            </p>
                            <p
                                class="font-light text-gray-800 sm:text-lg dark:text-gray-400"
                            >
                                Talktime: Unlimited
                            </p>
                            <button
                                type="button"
                                data-plan="B"
                                class="plan-option text-white bg-accent-color hover:bg-accent-color-hover focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm mt-4 px-5 py-2.5 text-center dark:text-white dark:focus:ring-primary-900"
                            >
                                Select B
                            </button>
                        </div>
                        <!-- Pricing Card -->
                        <div
                            class="flex flex-col w-full p-6 mx-auto max-w-lg text-center text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white"
                        >
                            <div
                                class="flex justify-center items-baseline my-8"
                            >
                                <span class="mr-2 text-5xl font-extrabold"
                                    >₹39.9</span
                                >
                            </div>
                            <h3 class="mb-4 text-xl font-semibold">
                                Validity: 28days
                            </h3>
                            <p
                                class="font-medium text-sm text-gray-800 sm:text-lg dark:text-gray-400"
                            >
                                Data: 1.0GB/day
                            </p>
                            <p
                                class="font-light text-gray-800 sm:text-lg dark:text-gray-400"
                            >
                                Talktime: Unlimited
                            </p>
                            <button
                                type="button"
                                data-plan="C"
                                class="plan-option text-white bg-accent-color hover:bg-accent-color-hover focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm mt-4 px-5 py-2.5 text-center dark:text-white dark:focus:ring-primary-900"
                            >
                                Select C
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <button class="submit-button" type="submit">Activate</button>
        </form>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const planOptions = document.querySelectorAll(".plan-option");

            planOptions.forEach((option) => {
                option.addEventListener("click", function () {
                    document.getElementById("selectedPlan").value =
                        this.dataset.plan;
                });
            });
        });
    </script>
</body>
</html>
