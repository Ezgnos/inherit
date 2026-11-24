<?php
declare(strict_types=1);

// Задача 2.2. Форматирование отчётов

interface ReportFormatterInterface
{
    public function format(array $data): string;
}

interface ReportHeaderInterface
{
    public const SEPARATOR = '========================================';

    public function getHeader(): string;
}

class HtmlFormatter implements ReportFormatterInterface
{
    public function format(array $data): string
    {
        $items = '';
        foreach ($data as $item) {
            $items .= '<li>' . htmlspecialchars((string) $item) . '</li>';
        }
        return '<ul>' . $items . '</ul>';
    }
}

class CsvFormatter implements ReportFormatterInterface
{
    public function format(array $data): string
    {
        return implode(';', $data);
    }
}

class SalesReport implements ReportFormatterInterface, ReportHeaderInterface
{
    /** @param array<string, float|int> $sales  продукт => сумма */
    public function __construct(private array $sales) {}

    public function getHeader(): string
    {
        return 'Отчёт о продажах за текущий период';
    }

    // Если $data не передан, форматируются собственные продажи отчёта.
    // Параметр с значением по умолчанию не нарушает контракт интерфейса.
    public function format(array $data = []): string
    {
        $data = $data === [] ? $this->sales : $data;
        $lines = [];
        foreach ($data as $product => $sum) {
            $lines[] = "Продукт: {$product} — сумма: " . number_format((float) $sum, 0, '', ' ') . ' руб.';
        }
        return implode(PHP_EOL, $lines);
    }
}

function renderReport(ReportFormatterInterface $report, array $data = []): void
{
    echo $report->format($data) . PHP_EOL;
}

// ---- Демонстрация ----
$items = ['Ноутбук', 'Мышь', 'Монитор'];

echo 'HTML: ';
renderReport(new HtmlFormatter(), $items);
echo 'CSV:  ';
renderReport(new CsvFormatter(), $items);

echo PHP_EOL;
$report = new SalesReport(['Ноутбук' => 120000, 'Мышь' => 1500, 'Монитор' => 25000]);

// Тот же объект как ReportHeaderInterface: заголовок через интерфейс
function printHeader(ReportHeaderInterface $r): void
{
    echo $r->getHeader() . PHP_EOL;
}

printHeader($report);
echo ReportHeaderInterface::SEPARATOR . PHP_EOL;
renderReport($report); // тот же объект как ReportFormatterInterface
echo ReportHeaderInterface::SEPARATOR . PHP_EOL;

echo 'SalesReport::SEPARATOR совпадает: ' . var_export(SalesReport::SEPARATOR === ReportHeaderInterface::SEPARATOR, true) . PHP_EOL;
var_dump($report instanceof ReportFormatterInterface, $report instanceof ReportHeaderInterface);
