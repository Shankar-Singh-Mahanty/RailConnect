<?php

include 'authenticate.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
checkUser($role);

// Include database connection script
include 'db_connect.php';


// Fetch plans
$query = "SELECT * FROM plans";
$result = $conn->query($query);
$plans = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }
}

// Fetch departments
$query = "SELECT * FROM departments";
$result = $conn->query($query);
$departments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Fetch operators
$query = "SELECT * FROM operators";
$result = $conn->query($query);
$operators = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $operators[] = $row;
    }
}

// Fetch units
$query = "SELECT * FROM units";
$result = $conn->query($query);
$units = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $units[] = $row;
    }
}


// Query to fetch all plans from the database
$query = "SELECT * FROM plans";
$result = $conn->query($query);

// Check if any plans are found
$plans = [];
if ($result->num_rows > 0) {
    // Fetch all plans
    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }
}

// Add CUG Form ------------------------------------

$name = "";
$cug_no = "";
$emp_no = "";
$designation = "";
$unit = "default";
$department = "default";
$bill_unit_no = "";
$allocation = "";
$operator = "default";
$plan = "";

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
    if (empty($name))
        $errors[] = "Name is required.";
    if (empty($cug_no)) {
        $errors[] = "CUG No is required.";
    } elseif (!is_numeric($cug_no) || (strlen($cug_no) != 10) || $cug_no <= 0) {
        $errors[] = "CUG No must be a positive numeric value and 10 digits long.";
    }
    if (empty($emp_no)) {
        $errors[] = "Employee No is required.";
    } elseif (!is_numeric($emp_no) || strlen($emp_no) != 12 || $emp_no <= 0) {
        $errors[] = "Employee No must be a 12-digit numeric value and positive.";
    }
    if (empty($designation))
        $errors[] = "Designation is required.";
    if ($unit == "default")
        $errors[] = "Unit is required.";
    if ($department == "default")
        $errors[] = "Department is required.";
    if (empty($bill_unit_no))
        $errors[] = "Bill Unit No is required.";
    if (empty($allocation) || !is_numeric($allocation))
        $errors[] = "Allocation must be a positive numeric value.";
    if ($operator == "default")
        $errors[] = "Operator is required.";
    if (empty($plan) || !in_array($plan, ['A', 'B', 'C']))
        $errors[] = "Invalid plan selected.";

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
    } else {
        // Prepare and execute insertion into cugdetails
        $stmt_cug = $conn->prepare("INSERT INTO cugdetails (cug_number, emp_number, empname, designation, unit, department, bill_unit_no, allocation, operator, plan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt_cug === false) {
            $_SESSION['errors'] = ["Error preparing cugdetails statement: " . $conn->error];
        } else {
            $stmt_cug->bind_param("iisssssdsss", $cug_no, $emp_no, $name, $designation, $unit, $department, $bill_unit_no, $allocation, $operator, $plan, $status);
            if ($stmt_cug->execute()) {
                $_SESSION['success'] = "CUG is successfully allotted to $name";

                // Insert into cugdetails_transaction after successful insertion into cugdetails
                $stmt_transaction = $conn->prepare("INSERT INTO cugdetails_transaction (cug_number, emp_number, empname, designation, unit, department, bill_unit_no, allocation, operator, plan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt_transaction === false) {
                    $_SESSION['errors'] = ["Error preparing transaction statement: " . $conn->error];
                } else {
                    $stmt_transaction->bind_param("iisssssdsss", $cug_no, $emp_no, $name, $designation, $unit, $department, $bill_unit_no, $allocation, $operator, $plan, $status);
                    if ($stmt_transaction->execute()) {
                        $_SESSION['success'] .= " & Transaction recorded.";
                    } else {
                        $_SESSION['errors'] = ["Error inserting transaction: " . $stmt_transaction->error];
                    }
                    $stmt_transaction->close();
                }
            } else {
                $_SESSION['errors'] = ["Error inserting into cugdetails: " . $stmt_cug->error];
            }
            $stmt_cug->close();
        }
    }

    header("Location: add-cug.php");
    exit();
}

if (isset($_SESSION['form_data'])) {
    $name = $_SESSION['form_data']['NAME'];
    $cug_no = $_SESSION['form_data']['CUG_NO'];
    $emp_no = $_SESSION['form_data']['EMP_NO'];
    $designation = $_SESSION['form_data']['DESIGNATION'];
    $unit = $_SESSION['form_data']['UNIT'];
    $department = $_SESSION['form_data']['DEPARTMENT'];
    $bill_unit_no = $_SESSION['form_data']['BILL_UNIT_NO'];
    $allocation = $_SESSION['form_data']['ALLOCATION'];
    $operator = $_SESSION['form_data']['OPERATOR'];
    $plan = $_SESSION['form_data']['PLAN'];
    unset($_SESSION['form_data']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add CUG</title>
    <link rel="icon" type="image/webp" href="logo.webp" />
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
                        "Nunito Sans",
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
                        "Nunito Sans",
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
    <header>
        <div class="header-top">
            <a href="./">
                <h1>East Coast Railway</h1>
                <h1>Closed User Group</h1>
            </a>
        </div>
    </header>
    <main id="main">
        <div class="heading-container">
            <button class="back-btn" id="roleRedirectButton" data-role="<?php echo $role; ?>">
                <img src="icon/back-button.webp" alt="back button">
            </button>
            <h2 class="heading">Allotment Of New Cug</h2>
        </div>
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="session-message error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="session-message success">
                <p><?php echo $_SESSION['success']; ?></p>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form class="form_container" action="add-cug.php" method="post">
            <div class="input_box long-input">
                <label for="cugno">CUG No</label>
                <input class="py-2 px-3" type="number" placeholder="Enter CUG no." name="CUG_NO"
                    value="<?php echo htmlspecialchars($cug_no); ?>" required />
            </div>
            <div class="input_box">
                <label for="name">Name</label>
                <input class="py-2 px-3" type="text" placeholder="Enter Name" name="NAME"
                    value="<?php echo htmlspecialchars($name); ?>" required />
            </div>
            <div class="input_box">
                <label for="empno">Employee No.</label>
                <input class="py-2 px-3" type="number" placeholder="Enter Employee no." name="EMP_NO"
                    value="<?php echo htmlspecialchars($emp_no); ?>" required />
            </div>
            <div class="input_box">
                <label for="designation">Designation</label>
                <input class="py-2 px-3" type="text" placeholder="Enter Designation" name="DESIGNATION"
                    value="<?php echo htmlspecialchars($designation); ?>" required />
            </div>
            <div class="input_box">
                <label for="unit">Unit</label>
                <select class="py-2 px-2" id="unit" name="UNIT" required>
                    <option value="default" <?php if ($unit == 'default')
                        echo 'selected'; ?>>Select unit</option>
                    <?php foreach ($units as $unit_option): ?>
                        <option value="<?php echo $unit_option['name']; ?>" <?php echo ($unit == $unit_option['name']) ? 'selected' : ''; ?>><?php echo $unit_option['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input_box">
                <label for="department">Department</label>
                <select class="py-2 px-2" id="department" name="DEPARTMENT" required>
                    <option value="default" <?php if ($department == 'default')
                        echo 'selected'; ?>>Select department
                    </option>
                    <?php foreach ($departments as $department_option): ?>
                        <option value="<?php echo $department_option['name']; ?>" <?php echo ($department == $department_option['name']) ? 'selected' : ''; ?>>
                            <?php echo $department_option['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input_box">
                <label for="bill_unit_no">Bill Unit No</label>
                <input class="py-2 px-3" type="text" placeholder="Enter Bill Unit No" name="BILL_UNIT_NO"
                    value="<?php echo htmlspecialchars($bill_unit_no); ?>" required />
            </div>
            <div class="input_box">
                <label for="allocation">Allocation</label>
                <input class="py-2 px-3" type="text" placeholder="Enter Allocation" name="ALLOCATION"
                    value="<?php echo htmlspecialchars($allocation); ?>" required />
            </div>
            <div class="input_box">
                <label for="operator">Operator</label>
                <select class="py-2 px-2" id="operator" name="OPERATOR" required>
                    <option value="default" <?php if ($operator == 'default')
                        echo 'selected'; ?>>Select operator</option>
                    <?php foreach ($operators as $operator_option): ?>
                        <option value="<?php echo $operator_option['name']; ?>" <?php echo ($operator == $operator_option['name']) ? 'selected' : ''; ?>>
                            <?php echo $operator_option['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input_box">
                <!-- <label for="plan">Plan:</label> -->
                <input class="py-2 px-3" type="hidden" id="selectedPlan" name="PLAN" readonly required />
            </div>
            <section>
                <div class="py-4 px-4 mx-auto max-w-screen-xl lg:px-6">
                    <div class="mx-auto max-w-screen-md text-center mb-2 lg:mb-6">
                        <h2 class="text-2xl tracking-tight font-extrabold text-white">
                            Choose a Plan
                        </h2>
                    </div>
                    <div class="space-y-8 lg:grid lg:grid-cols-3 sm:gap-6 xl:gap-10 lg:space-y-0">
                        <?php foreach ($plans as $plan): ?>
                            <div
                                class="plan-container flex flex-col w-full p-6 mx-auto max-w-lg text-center text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white">
                                <div class="flex justify-center items-baseline my-8">
                                    <span
                                        class="mr-2 text-5xl font-extrabold">₹<?= number_format($plan['price'], 2) ?></span>
                                </div>
                                <h3 class="mb-4 text-xl font-semibold">
                                    Validity: <?= $plan['validity_days'] ?>days
                                </h3>
                                <p class="font-medium text-sm text-gray-800 sm:text-lg dark:text-gray-400">
                                    Data: <?= $plan['data_per_day'] ?>GB/day
                                </p>
                                <p class="font-light text-gray-800 sm:text-lg dark:text-gray-400">
                                    Talktime: <?= $plan['talktime'] ?>
                                </p>
                                <button type="button" data-plan="<?= $plan['plan_name'] ?>"
                                    class="plan-option text-white bg-accent-color hover:bg-accent-color-hover focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm mt-4 px-5 py-2.5 text-center dark:text-white dark:focus:ring-primary-900">
                                    Select <?= $plan['plan_name'] ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <button type="submit" class="submit-button">Submit</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 East Coast Railway. All rights reserved.</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function ()
        {
            const planOptions = document.querySelectorAll(".plan-option");

            planOptions.forEach((option) =>
            {
                option.addEventListener("click", function ()
                {
                    document.getElementById("selectedPlan").value =
                        this.dataset.plan;

                    const planContainers = document.querySelectorAll(".plan-container");
                    planContainers.forEach(container =>
                    {
                        container.classList.remove("selected-option");
                    });

                    const parentElement = this.parentNode;
                    parentElement.classList.add("selected-option");
                });
            });

            // Role based Redirection -------------------------
            const redirectButton = document.getElementById("roleRedirectButton");
            const userRole = redirectButton.getAttribute("data-role");

            redirectButton.addEventListener("click", function ()
            {
                if (userRole === 'admin')
                {
                    window.location.href = 'admin-page.php';
                } else if (userRole === 'dealer')
                {
                    window.location.href = 'dealer-page.php';
                } else
                {
                    alert("Error: Unexpected role. Please login again.");
                }
            });

        });
    </script>

</body>

</html>