<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Login</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="login.css">
    <style>
        .error {
            color: red;
            margin-top: 10px;
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-3px); }
            100% { transform: translateX(3px); }
        }
        .back-home {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #333;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <form action="dealerLoginServlet" method="post" class="login">
    <a href="index.html" class="back-home">Back to Home Page</a>
        <h2>Welcome, Dealer!</h2>
        <p>Please log in</p>
        <input type="username" placeholder="Username" name="name" required>
        <input type="password" placeholder="Password" name="password" required>
        <input type="submit" value="Log In">
        <% 
            HttpSession session2 = request.getSession(false);
            if (session2 != null) {
                String errorMessage = (String) session2.getAttribute("errorMessage");
                if (errorMessage != null) {
        %>
                    <p class="error"><%= errorMessage %></p>
        <%
                    session2.removeAttribute("errorMessage"); // Remove error message after displaying it
                }
            }
        %>
    </form>
</body>
</html>
