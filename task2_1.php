<?php
declare(strict_types=1);

// Задача 2.1. Способы оплаты

interface PaymentInterface
{
    public function pay(float $amount): bool;
    public function refund(float $amount): bool;
    public function getMethodName(): string;
}

class CreditCard implements PaymentInterface
{
    public function __construct(private float $limit) {}

    public function pay(float $amount): bool
    {
        return $amount > 0 && $amount <= $this->limit;
    }

    public function refund(float $amount): bool
    {
        return $amount > 0; // в задании правило возврата не задано — принимаем любую положительную сумму
    }

    public function getMethodName(): string
    {
        return 'Кредитная карта';
    }
}

class PayPal implements PaymentInterface
{
    public function __construct(private float $balance) {}

    public function pay(float $amount): bool
    {
        if ($amount <= 0 || $amount > $this->balance) {
            return false;
        }
        $this->balance -= $amount;
        return true;
    }

    public function refund(float $amount): bool
    {
        if ($amount <= 0) {
            return false;
        }
        $this->balance += $amount;
        return true;
    }

    public function getMethodName(): string
    {
        return 'PayPal';
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
}

class Cash implements PaymentInterface
{
    public function pay(float $amount): bool
    {
        return true; // всегда успешно
    }

    public function refund(float $amount): bool
    {
        return true; // всегда успешно
    }

    public function getMethodName(): string
    {
        return 'Наличные';
    }
}

// Функция работает с любым объектом, реализующим интерфейс
function checkout(PaymentInterface $method, float $amount): void
{
    $ok = $method->pay($amount);
    echo sprintf(
        '[%s] оплата %s руб.: %s',
        $method->getMethodName(),
        number_format($amount, 2, ',', ' '),
        $ok ? 'успешно' : 'ОТКАЗ'
    ) . PHP_EOL;
}

// ---- Демонстрация ----
$card = new CreditCard(50000);
checkout($card, 30000);   // успешно
checkout($card, 70000);   // отказ: выше лимита

$paypal = new PayPal(10000);
checkout($paypal, 4000);  // успешно, баланс 6000
checkout($paypal, 8000);  // отказ: недостаточно средств
$paypal->refund(4000);    // возврат, баланс 10000
echo 'Баланс PayPal после возврата: ' . number_format($paypal->getBalance(), 2, ',', ' ') . PHP_EOL;

// По условию Cash всегда успешен, поэтому неуспешную оплату для него показать нельзя.
$cash = new Cash();
checkout($cash, 1500);
checkout($cash, 99999999);
echo 'Возврат наличными: ' . var_export($cash->refund(500), true) . PHP_EOL;
