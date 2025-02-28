<?php
include('connection/conn.php');
session_start();

if(isset($_POST['submitLogin'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if(empty($email) || empty($password)) {
        $error = "Please fill up all fields";
    } else {
        $db = new DatabaseConnector();
        $sql = "SELECT *,department.Department_Description,campus.Campus_Description 
                FROM account_user 
                LEFT JOIN department ON department.Department_No = account_user.Department 
                LEFT JOIN campus ON campus.Campus_No = account_user.Campus 
                WHERE Email = ? AND Password = SHA2(?, 256)";
        $result = $db->fetch($sql, [$email, $password]);
        if ($result) {
            // record last login
            $updateLastLogin = "UPDATE account_user SET Last_Login = CURRENT_TIMESTAMP() WHERE User_No = ?";
            $db->execute($updateLastLogin, [$result['User_No']]);
            // set session
            $_SESSION['userno'] = $result['User_No'];
            $_SESSION['fullname'] = $result['Firstname'] ." ". substr($result['Middlename'], 0, 1). "." . $result['Lastname'];
            $_SESSION['campus'] = $result['Campus_Description'];
            $_SESSION['departmentno'] = $result['Department_No'];
            $_SESSION['department'] = $result['Department_Description'];
            $_SESSION['accounttype'] = $result['Account_Type'];
            $_SESSION['email'] = $result['Email'];
            header('Location: user/dashboard.php');
            exit();
        } else {
            $error = "Incorrect Email or Password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorization - VP Unit DMS</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="node_modules/toastr/build/toastr.min.css">
    <script src="node_modules/toastr/build/toastr.min.js"></script>
</head>
<style>
    body {
        background-image: url("assets/img/bg.png");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        min-height: 100vh;
    }

    .login-container {
        background: rgba(255, 193, 7, 0.9);
        backdrop-filter: blur(5px);
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 2rem;
        width: 100%;
        max-width: 400px;
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        padding: 0.8rem;
        margin-top: 0.5rem;
        border-radius: 8px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
        background-color: white;
    }

    .btn-primary {
        background-color: #0d47a1;
        border: none;
        padding: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #1565c0;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 71, 161, 0.3);
    }

    .form-label {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .logo-container {
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo-container img {
        width: 80px;
        height: 80px;
        margin-bottom: 1rem;
    }

    .title {
        color: #2c3e50;
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 2rem;
    }

    .custom-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background-color: #ff4444;
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: none;
        z-index: 1000;
        animation: all 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
<body class="d-flex justify-content-center align-items-center">
    <div class="login-container">
        <div class="logo-container">
            <img src="assets/img/logo.png" alt="JRMSU Logo" class="img-fluid">
            <h2 class="title">Authorization</h2>
        </div>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" placeholder="example@gmail.com">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" name="password" placeholder="********" id="passwordInput">
                    <button class="btn btn-link position-absolute top-50 end-0 translate-middle-y pe-3" type="button" id="togglePassword" style="border: none;">
                        <i class="bi bi-eye-slash fs-3"></i>
                    </button>
                </div>
            </div>
            <button type="submit" name="submitLogin" class="btn btn-primary w-100">Sign In</button>
        </form>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script>
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "2000"
};
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('passwordInput');
    const icon = this.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }
});
</script>
<?php if (isset($error)): ?>
    <script>
        toastr.error("<?php echo $error; ?>");
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        toastr.success("Login Success! Redirecting to dashboard...");
    </script>
<?php endif; ?>
</html>