<?php
include "login/connect.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NoteBookStore</title>
  <style>
    /* Universal Selector */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    /*  Type Selector */
    body {
      font-family: Arial, sans-serif;
      font-size: 14px;
      background-color: #f2f2f2;
      line-height: 1.6;
    }

    /*  Semantic layout */
    header,
    nav,
    main,
    footer {
      display: block;
      width: 100%;
    }

    /* Class Selector */
    .nav-left a {
      color: white;
      text-decoration: none;
      margin-right: 15px;
      font-weight: bold;
    }

    /* ID Selector */
    #main-title {
      font-size: 19px;
      color: #fff;
      letter-spacing: 1px;
    }

    /*  Child Selector */
    nav>.nav-left>a:hover {
      color: #ffffffff;
    }

    /*  Adjacent Sibling Selector */
    .nav-left a+a {
      padding-left: 10px;
    }


    /* Attribute Selector */
    a[href*="login"] {
      color: #ffffffff;
      text-decoration: none;
      font-weight: bold;
    }

    a[href*="logout"] {
      color: #ffffffff;
      text-decoration: none;
      font-weight: bold;
    }

    /*  Pseudo-class Selector */
    a:hover {
      text-decoration: underline;
    }

    /* Pseudo-element Selector */
    .nav-right::after {
      color: #fff;
      font-style: italic;
    }

    /* Mobile First Layout */
    nav {
      background-color: #000000ff;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding: 10px 20px;
      position: relative;

    }

    /* Navbar sections */
    .nav-left,
    .nav-right {
      width: 100%;
    }

    /* Menu links */
    .nav-left a,
    .nav-right a {
      color: #ffffffff;
      display: block;
      margin: 8px 0;
    }

    /* Hamburger icon */
    .icon {
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 24px;
      color: white;
      cursor: pointer;
      background: none;
      border: none;
    }

    /* Hide menu by default on mobile */
    #menu-links {
      display: none;
      width: 100%;
    }

    /* Show menu when active */
    #menu-links.active {
      display: block;
    }

    /* Hover effect */
    .nav-right a:hover {
      color: #ffffffff;
    }

    /* Responsive for Desktop */
    @media (min-width: 769px) {
      nav {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
      }

      .icon {
        display: none;
      }

      #menu-links {
        display: flex !important;
        gap: 20px;
      }

      .nav-left a,
      .nav-right a {
        display: inline-block;
        margin: 0 10px;
      }

      .nav-left {
        flex: 1;
      }

      .nav-right {
        flex: 1;
        text-align: right;
      }
    }
  </style>
</head>

<body>
  <header>
    <nav>
      <strong id="main-title">NOTEBOOKSTORE</strong>

      <!-- Hamburger icon (mobile only) -->
      <button class="icon" onclick="toggleMenu()">☰</button>

      <!-- Menu -->
      <div id="menu-links">
        <div class="nav-left">
          <a href="../product/product.php">Home</a>
          <a href="../product/cart.php">Cart</a>
          <a href="../notification/notification.php">Notification</a>
        </div>

        <div class="nav-right">
          <?php
          if (isset($_SESSION['username'])) {
            echo "🙍🏻‍♂️ <b><a href='../customer/customer.php'>" . $_SESSION['username'] . "</a></b> ";
            echo "<a href='../login/logout.php'>logout</a>";
          } else {
            echo "<a href='../login/login-form.php'>login</a>";
          }
          ?>
        </div>
      </div>
    </nav>
  </header>

  <script>
    function toggleMenu() {
      document.getElementById("menu-links").classList.toggle("active");
    }
  </script>
</body>

</html>