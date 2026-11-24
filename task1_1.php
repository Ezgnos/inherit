<?php
declare(strict_types=1);

// Задача 1.1. Иерархия транспортных средств

class Vehicle
{
    protected string $brand;
    protected int $maxSpeed;

    public function __construct(string $brand, int $maxSpeed)
    {
        $this->brand = $brand;
        $this->maxSpeed = $maxSpeed;
    }

    public function getInfo(): string
    {
        return "Марка: {$this->brand}, макс. скорость: {$this->maxSpeed} км/ч";
    }
}

class Car extends Vehicle
{
    private string $fuelType;

    public function __construct(string $brand, int $maxSpeed, string $fuelType)
    {
        parent::__construct($brand, $maxSpeed); // не дублируем инициализацию родителя
        $this->fuelType = $fuelType;
    }

    public function getInfo(): string
    {
        return parent::getInfo() . ", тип топлива: {$this->fuelType}";
    }
}

class Airplane extends Vehicle
{
    private float $wingspan;

    public function __construct(string $brand, int $maxSpeed, float $wingspan)
    {
        parent::__construct($brand, $maxSpeed);
        $this->wingspan = $wingspan;
    }

    public function getInfo(): string
    {
        return parent::getInfo() . ", размах крыльев: {$this->wingspan} м";
    }
}

// ---- Демонстрация ----
$vehicles = [
    new Car('Toyota Camry', 210, 'бензин'),
    new Airplane('Boeing 737', 950, 35.8),
];

foreach ($vehicles as $v) {
    echo $v->getInfo() . PHP_EOL;
}

echo PHP_EOL;
foreach ($vehicles as $v) {
    echo get_class($v) . ' instanceof Vehicle: ' . var_export($v instanceof Vehicle, true) . PHP_EOL;
}
