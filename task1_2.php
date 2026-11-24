<?php
declare(strict_types=1);

// Задача 1.2. Система сотрудников

/*
 * Employee — абстрактный класс, поэтому `new Employee(...)` вызовет Error
 * («Cannot instantiate abstract class Employee»). Он содержит абстрактный метод
 * calculateSalary() без реализации: у «просто сотрудника» нет правила расчёта
 * зарплаты, оно появляется только в конкретных наследниках. Абстрактный класс
 * задаёт общий шаблон, а создавать можно только полностью определённые классы.
 */
abstract class Employee
{
    public function __construct(
        protected string $name,
        protected float $baseSalary,
    ) {}

    abstract public function calculateSalary(): float;

    public function getName(): string
    {
        return $this->name;
    }
}

class Manager extends Employee
{
    public function calculateSalary(): float
    {
        return $this->baseSalary * 1.20; // оклад + 20% премия
    }
}

class Developer extends Employee
{
    private int $overtimeHours = 0;

    public function setOvertimeHours(int $hours): void
    {
        if ($hours < 0) {
            throw new InvalidArgumentException('Часы переработки не могут быть отрицательными');
        }
        $this->overtimeHours = $hours;
    }

    public function calculateSalary(): float
    {
        return $this->baseSalary + $this->overtimeHours * 500;
    }
}

class Intern extends Employee
{
    public function calculateSalary(): float
    {
        return $this->baseSalary * 0.5;
    }
}

function money(float $v): string
{
    return number_format($v, 2, ',', ' ') . ' руб.';
}

/** @param Employee[] $employees */
function printPayroll(array $employees): void
{
    $total = 0.0;
    echo 'Имя — Зарплата' . PHP_EOL;
    echo str_repeat('-', 40) . PHP_EOL;
    foreach ($employees as $e) {
        $salary = $e->calculateSalary(); // полиморфизм: у каждого класса своя формула
        $total += $salary;
        echo $e->getName() . ' — ' . money($salary) . PHP_EOL;
    }
    echo str_repeat('-', 40) . PHP_EOL;
    echo 'Общий фонд оплаты труда: ' . money($total) . PHP_EOL;
}

// ---- Демонстрация ----
$dev = new Developer('Алексей Смирнов', 80000);
$dev->setOvertimeHours(10);

printPayroll([
    new Manager('Ирина Петрова', 100000),
    $dev,
    new Intern('Олег Иванов', 40000),
]);

// Попытка создать абстрактный класс:
try {
    $x = new Employee('Тест', 1000); // PHP 8: Error
} catch (Error $e) {
    echo PHP_EOL . 'Ошибка: ' . $e->getMessage() . PHP_EOL;
}
