<?php

require_once __DIR__ . '/../init-app.php';
use Database\MySQLWrapper;

$mysqli = new MySQLWrapper();

$result = $mysqli->query(file_get_contents(__DIR__ . '/Examples/cars-setup.sql'));
if ($result === false) throw new Exception('Could not execute query for cars.');

/**
 * cars と 1対多の car_parts テーブルを作成
 * - 外部キー: car_parts.carID -> cars.id
 * - 車が削除されたら紐づく部品も削除（ON DELETE CASCADE）
 */
$result = $mysqli->query("
    CREATE TABLE IF NOT EXISTS car_parts (
      id INT PRIMARY KEY AUTO_INCREMENT,
      carID INT,
      name VARCHAR(255),
      description TEXT,
      Price FLOAT,
      quantityInStock INT,
      FOREIGN KEY (carID) REFERENCES cars(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
if ($result === false) throw new Exception('Could not execute query for car_parts.');

print("Successfully ran all SQL setup queries." . PHP_EOL);

function insertCarQuery(
  string $make,
  string $model,
  int $year,
  string $color,
  float $price,
  float $mileage,
  string $transmission,
  string $engine,
  string $status
): string {
  return sprintf(
      "INSERT INTO cars (make, model, year, color, price, mileage, transmission, engine, status)
      VALUES ('%s', '%s', %d, '%s', %f, %f, '%s', '%s', '%s');",
      $make, $model, $year, $color, $price, $mileage, $transmission, $engine, $status
  );
}

function insertPartQuery(
  string $name,
  string $description,
  float $price,
  int $quantityInStock
): string {
  return sprintf(
      "INSERT INTO car_parts (name, description, price, quantityInStock)
      VALUES ('%s', '%s', %f, %d);",
      $name, $description, $price, $quantityInStock
  );
}

function runQuery(mysqli $mysqli, string $query): void {
  $result = $mysqli->query($query);
  if ($result === false) {
      throw new Exception('Could not execute query.');
  } else {
      echo "Query executed successfully.\n";
  }
}

runQuery($mysqli, insertCarQuery(
  make: 'Toyota',
  model: 'Corolla',
  year: 2020,
  color: 'Blue',
  price: 20000,
  mileage: 1500,
  transmission: 'Automatic',
  engine: 'Gasoline',
  status: 'Available'
));

runQuery($mysqli, insertPartQuery(
  name: 'Brake Pad',
  description: 'High Quality Brake Pad',
  price: 45.99,
  quantityInStock: 100
));

$mysqli->close();