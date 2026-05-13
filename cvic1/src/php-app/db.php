<?php

$host = 'mysql'; // Názov služby v docker-compose
$dbname = 'mydb';
$user = 'user';
$password = 'password';

try {
    // Vytvorenie PDO objektu pre MySQL
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "<h2>Pripojenie k databáze úspešné</h2>";

    // SQL dotaz na získanie všetkých riadkov z tabuľky "test"
    $stmt = $pdo->query("SELECT * FROM test");

    echo "<h3>Výsledky z tabuľky 'test':</h3>";
    echo "<table border='1'><tr>";

    // Načítanie hlavičiek
    $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($firstRow) {
        foreach (array_keys($firstRow) as $columnName) {
            echo "<th>$columnName</th>";
        }
        echo "</tr><tr>";

        // Výpis prvého riadku
        foreach ($firstRow as $value) {
            echo "<td>$value</td>";
        }
        echo "</tr>";

        // Výpis ďalších riadkov
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<p>Tabuľka 'test' je prázdna.</p>";
    }

    echo "</table>";

} catch (PDOException $e) {
    die("Chyba pripojenia k databáze: " . $e->getMessage());
}

?>