<?php
//These are the defined authentication environment in the db service

// The MySQL service named in the docker-compose.yml.
$host = 'db';

// Database use name
$user = 'MYSQL_USER';

//database user password
$pass = 'MYSQL_PASSWORD';

// database name
$mydatabase = 'MYSQL_DATABASE';
// check the mysql connection status

$conn = new mysqli($host, $user, $pass, $mydatabase);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected to MySQL server successfully!";
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Menu Example</title>
    <style>
        .menu-wrapper,
        .menu a {
            width: 100%;
        }

        .menu::after {
            content: '';
            clear: both;
            display: block;
        }

        .menu a {
            display: block;
            padding: 10px;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            text-decoration: none;
            font-size: 20px;
        }

        .menu li {
            position: relative;
        }

        .menu>li {
            float: left;
        }

        .menu,
        .menu ul {
            display: inline-block;
            padding: 0;
            margin: 0;
            list-style-type: none;
            background: gold;
        }

        .menu ul li+li {
            border-top: 1px solid #fff;
        }

        .menu ul {
            position: absolute;
            box-shadow: 5px 5px 10px 0 rgba(0, 0, 0, 0.5);
        }

        .menu>li ul,
        .menu ul ul {
            opacity: 0;
            -webkit-transition: all 0.2s ease-in;
            -moz-transition: all 0.2s ease-in;
            transition: all 0.2s ease-in;
            z-index: -1;
            visibility: hidden;
        }

        .menu>li ul {
            top: 130%;
            left: 0;
        }

        .menu ul ul {
            left: 130%;
            top: 0;
        }

        .menu ul a {
            width: 250px;
        }

        .menu>li:hover>ul {
            top: 100%;
            opacity: 1;
            z-index: 1;
            visibility: visible;
        }

        .menu ul>li:hover>ul {
            left: 100%;
            opacity: 1;
            z-index: 1;
            visibility: visible;
        }

        .menu-gold,
        .menu-gold a {
            color: #000;
        }

        .menu-gold a:hover {
            background-color: #e6c300;
            color: #000;
        }

        body {
            background-image: url("http://recruit.framgia.vn/wp-content/themes/framgia-vn/css/images/bg/banner.jpg");
            text-align: center;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #fff;
            font-size: 40px;
        }
    </style>
</head>

<body>
    <h1>Menu ngang đơn giản với HTML & CSS</h1>
    <div class="menu-wrapper menu-gold">
        <ul class="menu">
            <li><a href=""> Home</a></li>
            <li>
                <a href=""> Division 2</a>
                <ul>
                    <li>
                        <a href=""> Section 3</a>
                        <ul>
                            <li>
                                <a href=""> Group 1</a>
                                <ul>
                                    <li><a href=""> PHP</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</body>

</html>