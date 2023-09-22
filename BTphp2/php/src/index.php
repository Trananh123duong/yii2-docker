<?php

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

$sql = 'SELECT d.id, d.name, d.parentId, COUNT(f.id) AS files
    FROM directories AS d
    LEFT JOIN files AS f ON d.id = f.directoryId
    GROUP BY d.id, d.name
    ORDER BY parentId DESC';

$result = mysqli_query($conn, $sql);

$listDirectories = array();

while ($row = mysqli_fetch_assoc($result)) {
    $listDirectories[] = $row;
}

function calculateTotalFiles(&$listDirectories, $parentId = null)
{
    $totalFiles = 0;

    foreach ($listDirectories as &$item) {
        if ($item['parentId'] == $parentId) {
            $totalFiles += $item['files'];
            $totalFiles += calculateTotalFiles($listDirectories, $item['id']);
        }
    }

    return $totalFiles;
}

function showDirectories($listDirectories, $parent_id = 0, $char = '', $stt = 0)
{
    $stt++;
    foreach ($listDirectories as $key => $item) {
        if ($item['parentId'] == $parent_id) {
            echo '<tr >';
                echo '<td style="display: flex; align-items: center; height: 30px; padding: 0">';
                    for($i=$stt; $i>1; $i--) {
                        echo '<div class="square"></div>';
                    }
                    echo '<div style="margin-left: 10px">' . $item['name'] . '</div>';
                echo '</td>';

                echo '<td>';
                    echo $item['files'];
                echo '</td>';

                echo '<td>';
                    echo calculateTotalFiles($listDirectories, $item['id']) + $item['files'];
                echo '</td>';
            echo '</tr>';

            unset($listDirectories[$key]);

            showDirectories($listDirectories, $item['id'], $char . '|-----------', $stt);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .square {
            width: 80px;
            height: 100%;
            border: 2px solid;
            border-top: 0px solid while;
            border-bottom: 0px solid while;
            border-left: 0px solid while;
            border-right: 2px solid black;
        }
    </style>
</head>

<body>
    <table border="1" cellspacing="0" cellpadding="5">
        <tr>
            <td><strong>Tên</strong></td>
            <td><strong>Số file</strong></td>
            <td><strong>Tổng số file</strong></td>
        </tr>
        <?php showDirectories($listDirectories); ?>
    </table>
</body>

</html>
