<?php
declare(strict_types=1);

// Задача 3.2. Разрешение конфликтов трейтов

trait JsonExportTrait
{
    public function export(): string
    {
        // json_encode($this) кодирует только публичные свойства
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

    public function getFormat(): string
    {
        return 'JSON';
    }
}

trait XmlExportTrait
{
    public function export(): string
    {
        $tag = strtolower((new ReflectionClass($this))->getShortName());
        $xml = "<{$tag}>";
        foreach (get_object_vars($this) as $key => $value) {
            $xml .= "<{$key}>" . htmlspecialchars((string) $value) . "</{$key}>";
        }
        return $xml . "</{$tag}>";
    }

    public function getFormat(): string
    {
        return 'XML';
    }
}

trait PriceTrait
{
    // $tax — процент налога (20 означает 20%)
    public function getPriceWithTax(float $tax): float
    {
        return $this->price * (1 + $tax / 100);
    }
}

class Product
{
    use JsonExportTrait, XmlExportTrait, PriceTrait {
        JsonExportTrait::export insteadof XmlExportTrait;     // основной export() — JSON
        XmlExportTrait::export as exportToXml;                // XML под псевдонимом

        JsonExportTrait::getFormat insteadof XmlExportTrait;
        XmlExportTrait::getFormat as getXmlFormat;
        JsonExportTrait::getFormat as getJsonFormat;          // чтобы JSON-название осталось доступным
    }

    public function __construct(
        public string $name,
        public float $price,
    ) {}

    /*
     * Приоритет методов: метод самого класса > метод трейта > метод родителя.
     * Поэтому getFormat() вернёт «Product format», а не «JSON»: собственный метод
     * класса переопределяет метод, подключённый из трейта. Версия из трейта при этом
     * не потеряна — она доступна под псевдонимом getJsonFormat().
     */
    public function getFormat(): string
    {
        return 'Product format';
    }
}

// ---- Демонстрация ----
$p = new Product('Ноутбук', 1500.5);

echo 'JSON: ' . $p->export() . PHP_EOL;
echo 'XML:  ' . $p->exportToXml() . PHP_EOL;
echo 'getFormat():     ' . $p->getFormat() . PHP_EOL;     // Product format
echo 'getJsonFormat(): ' . $p->getJsonFormat() . PHP_EOL; // JSON
echo 'getXmlFormat():  ' . $p->getXmlFormat() . PHP_EOL;  // XML
echo 'Цена с налогом 20%: ' . number_format($p->getPriceWithTax(20), 2, ',', ' ') . ' руб.' . PHP_EOL;
