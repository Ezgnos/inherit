<?php
declare(strict_types=1);

// Задача 3.1. Логирование через трейт

trait LoggerTrait
{
    private array $logs = [];

    public function log(string $message): void
    {
        $this->logs[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function clearLogs(): void
    {
        $this->logs = [];
    }
}

/*
 * Почему трейт удобнее наследования: UserService и OrderService не связаны по смыслу
 * («сервис пользователей» не является «сервисом заказов»), общего родителя у них нет.
 * Делать ради логирования общий базовый класс — искусственно, да и PHP разрешает
 * только одного родителя, и он понадобится для настоящей иерархии. Трейт просто
 * добавляет код в класс, не занимая место родителя. У каждого класса при этом свой
 * экземпляр свойства $logs, поэтому журналы независимы.
 */
class UserService
{
    use LoggerTrait;

    public function register(string $username): void
    {
        // ... здесь была бы запись в БД ...
        $this->log("Зарегистрирован пользователь: {$username}");
    }
}

class OrderService
{
    use LoggerTrait;

    public function createOrder(int $orderId, float $total): void
    {
        $this->log("Создан заказ №{$orderId} на сумму " . number_format($total, 2, ',', ' ') . ' руб.');
    }
}

// ---- Демонстрация ----
$users = new UserService();
$users->register('anna');
$users->register('boris');

$orders = new OrderService();
$orders->createOrder(101, 2500.50);
$orders->createOrder(102, 99990);

echo "Журнал UserService:" . PHP_EOL;
print_r($users->getLogs());
echo "Журнал OrderService:" . PHP_EOL;
print_r($orders->getLogs());

$users->clearLogs();
echo PHP_EOL . 'После clearLogs() у UserService записей: ' . count($users->getLogs()) . PHP_EOL;
echo 'У OrderService записей: ' . count($orders->getLogs()) . ' (не изменился)' . PHP_EOL;
