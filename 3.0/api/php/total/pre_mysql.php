<?php
require('secret.php');
// 要插入的数组
$dataArray = array(
    "c14" => array("88227178","88227167","88227166","88227164","89627130","89627062","89627114","89627112","89627111","89624194","89627126","89627113"),
    "a2" => array("88227927","88227928","88227165"),
    "acar" => array("88227163","89626666"),
    "b7" => array("88227942","89623528"),
    "b10" => array("88227929","88227943"),
    "c8" => array("86060206","86060232","86062777","86062778","86062829","86060241","86060202","86062776","86060208","86060236"),
    "c7" => array("88232072","88232071","88232178","88232176"),
    "c12" => array("88232173","88232070","88232177","88232179"),
    "c13" => array("88232174","88232175","88232073","88232172"),
    "c8-1" => array("88230806","88230807","88230461","88230805","88230704","88230810","88230812","88230815","88230816","88230686")
);

$dataArray1 = array(
    "88232072" => "c7",
    "88232071" => "c7",
    "88232178" => "c7",
    "88232176" => "c7",
    
    "88232173" => "c12",
    "88232070" => "c12",
    "88232177" => "c12",
    "88232179" => "c12",
    
    "88232174" => "c13",
    "88232175" => "c13",
    "88232073" => "c13",
    "88232172" => "c13",
    
    "88230806" => "c8-1",
    "88230807" => "c8-1",
    "88230461" => "c8-1",
    "88230805" => "c8-1",
    "88230704" => "c8-1",
    "88230810" => "c8-1",
    "88230812" => "c8-1",
    "88230815" => "c8-1",
    "88230816" => "c8-1",
    "88230686" => "c8-1",
    
    "88227178" => "c14",
    "88227167" => "c14",
    "88227166" => "c14",
    "88227164" => "c14",
    "89627130" => "c14",
    "89627062" => "c14",
    "89627114" => "c14",
    "89627112" => "c14",
    "89627111" => "c14",
    "89624194" => "c14",
    "89627126" => "c14",
    "89627113" => "c14",
    
    "88227927" => "a2",
    "88227928" => "a2",
    "88227165" => "a2",
    
    "88227163" => "acar",
    "89626666" => "acar",
    
    "88227942" => "b7",
    "89623528" => "b7",
    
    "88227929" => "b10",
    "88227943" => "b10",
    
    "86060206" => "c8",
    "86060232" => "c8",
    "86062777" => "c8",
    "86062778" => "c8",
    "86062829" => "c8",
    "86060241" => "c8",
    "86060202" => "c8",
    "86062776" => "c8",
    "86060208" => "c8",
    "86060236" => "c8",
);

$servername = $Secret['mysql.server'];
$username = $Secret['mysql.username'];
$password = $Secret['mysql.password'];
$connname = $Secret['mysql.dbname'];

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);
// 检测连接
if ($conn->connect_error) {
    die('{"state": "error", "error": "connect error"}');
}

// 遍历数组并创建表
foreach ($dataArray as $tableName => $fieldNames) {
    $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (id VARCHAR(10) PRIMARY KEY";
    for ($i = 1; $i < 11; $i++) {
        $sql .= ", `$i` VARCHAR(13)";
    }
    $sql .= ")";

    echo '<br>' . $sql . '<br>';

    if ($conn->query($sql) === TRUE) {
        echo "表 $tableName 创建成功<br>";
    } else {
        echo "创建表 $tableName 失败: " . $conn->error . "<br>";
    }

    foreach ($fieldNames as $fieldName) {
        $sql = "INSERT IGNORE INTO `$tableName` (id";
        // 构建字段部分
        for ($i = 1; $i < 11; $i++) {
            $sql .= ", `$i`";
        }

        $sql .= ") VALUES ($fieldName";

        // 设置其他字段为0
        for ($i = 1; $i < 11; $i++) {
            $sql .= ", 0";
        }

        $sql .= ")";
        echo '<br>' . $sql . '<br>';

        if ($conn->query($sql) === TRUE) {
            echo "在表 $tableName 中插入数据 $i 成功<br>";
        } else {
            echo "在表 $tableName 中插入数据 $i 失败: " . $conn->error . "<br>";
        }
    }
}

$sql = "CREATE TABLE IF NOT EXISTS `data` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        `num` VARCHAR(8),
        `timestamp` VARCHAR(13),
        `using` BOOLEAN,
        `token` VARCHAR(40) NULL
        )";
echo '<br>' . $sql . '<br>';

if ($conn->query($sql) === TRUE) {
    echo "表 $tableName 创建成功<br>";
} else {
    echo "创建表 $tableName 失败: " . $conn->error . "<br>";
}

$sql = "INSERT IGNORE INTO `data` (id, `num`, `timestamp`, `using`, `token`) VALUES (
        1, 33566000, 1693057205000, 0, null
        )";

echo '<br>' . $sql . '<br>';

if ($conn->query($sql) === TRUE) {
    echo "在表 $tableName 中插入数据成功<br>";
} else {
    echo "在表 $tableName 中插入数据失败: " . $conn->error . "<br>";
}

// 关闭数据库连接
$conn->close();
?>
