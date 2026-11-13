<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        #[MapQueryParameter('country')] string $country,
        #[MapQueryParameter('city')] string $city,
        WeatherUtil $util,
        #[MapQueryParameter('format')] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false
    ): Response
    {
        $measurements = $util->getWeatherForCountryAndCity($country, $city);

        if ($format !== 'json' && $format !== 'csv') {
            return new Response('Invalid format', 400);
        }

        if ($twig) {
            $template = 'weather_api/index.' . $format . '.twig';

            $content = $this->renderView($template, [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ]);

            $contentType = $format === 'csv'
                ? 'text/csv; charset=UTF-8'
                : 'application/json; charset=UTF-8';

            return new Response($content, 200, [
                'Content-Type' => $contentType,
            ]);
        }

        if ($format === 'csv') {
            $csv = "city,country,date,celsius,fahrenheit\n";

            foreach ($measurements as $m) {
                $csv .= sprintf(
                    "%s,%s,%s,%s,%s\n",
                    $city,
                    $country,
                    $m->getDate()->format('Y-m-d'),
                    $m->getCelsius(),
                    $m->getFahrenheit()
                );

            }

            return new Response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(fn(Measurement $m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'celsius' => $m->getCelsius(),
                'fahrenheit' => $m->getFahrenheit(),
            ], $measurements),
        ]);
    }
}
