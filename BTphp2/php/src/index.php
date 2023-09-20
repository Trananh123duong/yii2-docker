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
    GROUP BY d.id, d.name';

$result = mysqli_query($conn, $sql);

$listDirectories = array();

while ($row = mysqli_fetch_assoc($result)) {
    $listDirectories[] = $row;
}

function showDirectories($listDirectories, $parent_id = 0, $char = '', $stt = 0)
{
    $stt++;
    foreach ($listDirectories as $key => $item) {
        if ($item['parentId'] == $parent_id) {
            echo '<tr>';
                echo '<td>';
                    echo $char . $item['name'];
                echo '</td>';

                echo '<td>';
                    echo $stt . ' - ' . $item['files'];
                echo '</td>';
            echo '</tr>';

            unset($listDirectories[$key]);

            showDirectories($listDirectories, $item['id'], $char . '|---', $stt);
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
