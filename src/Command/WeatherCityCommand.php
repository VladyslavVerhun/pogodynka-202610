<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'weather:city')]
final class WeatherCityCommand
{
    public function __construct(
        private WeatherUtil $weatherUtil
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Country code')] string $countryCode,
        #[Argument('City name')] string $city,
    ): int {
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $city);

        $io->writeln(sprintf('Location: %s, %s', $city, $countryCode));

        $io->table(['Date', 'Temperature'], array_map(fn($m) => [
            $m->getDate()->format('Y-m-d'),
            $m->getCelsius(),
        ], $measurements));

        return Command::SUCCESS;
    }
}
