<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    public static function dataGetFahrenheit(): array
    {
        return [
            ['0', '32'],
            ['-100', '-148'],
            ['100', '212'],
            ['0.5', '32.9'],
            ['-0.5', '31.1'],
            ['10', '50'],
            ['8', '46.4'],
            ['-40', '-40'],
            ['20', '68'],
            ['37.5', '99.5'],
        ];
    }

    #[DataProvider('dataGetFahrenheit')]
    public function testGetFahrenheit(
        string $celsius,
        string $expectedFahrenheit
    ): void {
        $measurement = new Measurement();
        $measurement->setCelsius($celsius);

        $this->assertEqualsWithDelta(
            (float) $expectedFahrenheit,
            $measurement->getFahrenheit(),
            0.00001
        );
    }
}
