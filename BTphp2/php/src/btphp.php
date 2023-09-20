<?php
function factorial($n) {
    if ($n <= 1) {
        return 1;
    } else {
        return $n * factorial($n - 1);
    }
}

function calculateExpression($n) {
    return pow(10, $n) / factorial($n);
}

// Nhập giá trị n từ người dùng
$n = intval(readline("Nhập số nguyên n: "));

// Tính giá trị biểu thức
$result = calculateExpression($n);

echo "Giá trị của 10^$n / $n! là: $result\n";
?>
