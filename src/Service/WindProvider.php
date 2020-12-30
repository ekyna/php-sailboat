<?php

namespace App\Service;

use DateTime;
use RuntimeException;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class WindProvider
 * @package App\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class WindProvider
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $forecastEndpoint;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var HttpClientInterface
     */
    private $client;


    /**
     * Constructor.
     *
     * @param string $projectDir
     * @param string $forecastEndpoint
     */
    public function __construct(string $projectDir, string $forecastEndpoint)
    {
        $this->projectDir = $projectDir;
        $this->forecastEndpoint = rtrim($forecastEndpoint, '/');

        $this->filesystem = new Filesystem();
        $this->client = HttpClient::create();
    }

    /**
     * Returns the wind direction.
     *
     * @param DateTime $date
     * @param float    $longitude
     * @param float    $latitude
     *
     * @return float The wind direction (degrees)
     */
    public function getDirection(DateTime $date, float $longitude, float $latitude): float
    {
        return $this->interpolate($date, $longitude, $latitude, false);
    }

    /**
     * Returns the wind speed.
     *
     * @param DateTime $date
     * @param float    $longitude
     * @param float    $latitude
     *
     * @return float The wind speed (m/s)
     */
    public function getSpeed(DateTime $date, float $longitude, float $latitude): float
    {
        return $this->interpolate($date, $longitude, $latitude, true);
    }

    /**
     * Interpolates the wind values.
     *
     * @param DateTime $date
     * @param float    $lon
     * @param float    $lat
     * @param bool     $speed
     *
     * @return float
     *
     * @see https://en.wikipedia.org/wiki/Bilinear_interpolation
     */
    private function interpolate(DateTime $date, float $lon, float $lat, bool $speed): float
    {
        $file = $this->download($date);

        $x1 = floor($lon);
        $y1 = floor($lat);
        $x2 = ceil($lon);
        $y2 = ceil($lat);

        $dx = $lon - $x1;
        $dy = $lat - $y1;

        $fx2y1 = $this->readAt($file, $x2, $y1, $speed);
        $fx1y1 = $this->readAt($file, $x1, $y1, $speed);
        $fx1y2 = $this->readAt($file, $x1, $y2, $speed);
        $fx2y2 = $this->readAt($file, $x2, $y2, $speed);

        $dfx = $fx2y1 - $fx1y1;
        $dfy = $fx1y2 - $fx1y1;
        $dfxy = $fx1y1 + $fx2y2 - $fx2y1 - $fx1y2;

        return $dfx * $dx + $dfy * $dy + $dfxy * $dx * $dy + $fx1y1;
    }

    /**
     * Reads the long/lat value.
     *
     * @param SplFileObject $file
     * @param float         $lon
     * @param float         $lat
     * @param bool          $speed Whether to read speed or direction.
     *
     * @return float
     */
    private function readAt(SplFileObject $file, float $lon, float $lat, bool $speed): float
    {
        // Translate from [-180, 180] to [0, 360]
        $lon = ($lon + 180) % 360;
        // Translate from [-90, 90] to [0, 180]
        $lat = ($lat + 90) % 180;

        /* Binary file structure is:
         *
         * direction (lon: -180, lat: -90)
         * speed     (lon: -180, lat: -90)
         * direction (lon: -179, lat: -90)
         * speed     (lon: -179, lat: -90)
         * direction (lon: -178, lat: -90)
         * speed     (lon: -178, lat: -90)
         * ...
         * direction (lon: -180, lat: -89)
         * speed     (lon: -180, lat: -89)
         * direction (lon: -179, lat: -89)
         * speed     (lon: -179, lat: -89)
         * ...
         * direction (lon: 179, lat: 90)
         * speed     (lon: 179, lat: 90)
         * direction (lon: 180, lat: 90)
         * speed     (lon: 180, lat: 90)
         *
         * Where speed and direction are packed as unsigned shorts.
         * One unsigned short is coded with 16 bits (2 bytes).
         */

        $offset = ($lat * 360 + $lon) * 4;
        if ($speed) {
            $offset += 2;
        }

        $file->fseek($offset);

        return current(unpack('S', $file->fread(2))) / 100;
    }

    /**
     * Downloads the wind file for the given date.
     *
     * @param DateTime $date
     *
     * @return SplFileObject
     */
    private function download(DateTime $date): SplFileObject
    {
        $date = clone $date;
        $date->setTime(intval($date->format('G') / 3) * 3, 0);

        $path = sprintf(
            'wind/%s/%s/%s-ds.wind',
            $date->format('Ymd'),
            $date->format('H'),
            '1p00'
        );

        $local = sprintf('%s/var/%s', $this->projectDir, $path);

        if ($this->filesystem->exists($local)) {
            return new SplFileObject($local, 'rb');
        }

        $remote = sprintf('%s/%s', $this->forecastEndpoint, $path);

        try {
            $response = $this->client->request('GET', $remote);
            if (Response::HTTP_OK !== $response->getStatusCode()) {
                throw new RuntimeException("Failed to download $remote");
            }
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException("Failed to download $remote");
        }

        $this->createDirectory(dirname($local));

        $file = new SplFileObject($path, 'wb');

        try {
            foreach ($this->client->stream($response) as $chunk) {
                $file->fwrite($chunk->getContent());
            }
        } catch (TransportExceptionInterface $e) {
            $file = null;

            throw new RuntimeException("Failed to write stream from $remote into $local.");
        }

        $file = null;

        return new SplFileObject($local, 'rb');
    }

    /**
     * Creates the directory if it not exists.
     *
     * @param string $path
     */
    private function createDirectory(string $path): void
    {
        if ($this->filesystem->exists($path)) {
            return;
        }

        $this->filesystem->mkdir($path);
    }
}
