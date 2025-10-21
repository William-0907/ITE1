<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>STAFF LOGIN</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #333;
  }

  .login-container {
    background: #fff;
    width: 380px;
    padding: 40px 40px 50px;
    border-radius: 12px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    text-align: center;
    animation: fadeInUp 0.7s ease forwards;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  h2 {
    margin-bottom: 30px;
    font-weight: 600;
    color: #1a1a1a;
    letter-spacing: 1.2px;
  }

  .input-group {
    margin-bottom: 25px;
    text-align: left;
  }

  .input-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #555;
    font-size: 14px;
    letter-spacing: 0.5px;
  }

  .input-group input[type="text"],
  .input-group input[type="password"] {
    width: 100%;
    padding: 14px 16px;
    font-size: 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    transition: border-color 0.3s, box-shadow 0.3s;
    outline: none;
  }

  .input-group input[type="text"]:focus,
  .input-group input[type="password"]:focus {
    border-color: #2575fc;
    box-shadow: 0 0 8px rgba(37, 117, 252, 0.5);
  }

  .btn-login {
    width: 100%;
    background: #2575fc;
    border: none;
    color: white;
    font-weight: 700;
    font-size: 16px;
    padding: 15px 0;
    border-radius: 8px;
    cursor: pointer;
    letter-spacing: 1px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 6px 12px rgba(37, 117, 252, 0.4);
  }

  .btn-login:hover {
    background: #1b56d9;
    box-shadow: 0 8px 20px rgba(27, 86, 217, 0.6);
  }

  .input-group input:focus-visible {
    outline: 3px solid #2575fc;
    outline-offset: 2px;
  }

  @media (max-width: 420px) {
    .login-container {
      width: 90%;
      padding: 30px 25px 40px;
    }
  }
</style>
</head>
<body>

<div class="login-container" role="main" aria-label="Staff login form">
  <h2>STAFF LOGIN</h2>
  <form method="POST" action="staff.php" novalidate autocomplete="off">
    <div class="input-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required autofocus autocomplete="off" />
    </div>

    <div class="input-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autocomplete="new-password" />
    </div>

    <button type="submit" class="btn-login" aria-label="Log in">Login</button>
  </form>
</div>

</body>
</html>
