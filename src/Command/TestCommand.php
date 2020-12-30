<?php

namespace App\Command;

use App\Service\WindProvider;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package App\Command
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TestCommand extends Command
{
    protected static $defaultName = 'app:test';

    /**
     * @var WindProvider
     */
    private $windProvider;

    /**
     * @var string
     */
    private $projectDir;


    /**
     * Constructor.
     *
     * @param WindProvider $windProvider
     * @param string $projectDir
     */
    public function __construct(WindProvider $windProvider, string $projectDir)
    {
        parent::__construct();

        $this->windProvider = $windProvider;
        $this->projectDir = $projectDir;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$this->write($output);
        //$this->read($output);

//        $data = $this->windProvider->read(new DateTime(), -180, -90);
//        $output->writeln('Data: ' . var_export($data, true));

        $date = new DateTime();
        $longitude = rand(-18000, 18000) / 100;
        $latitude = rand(-9000, 9000) / 100;

        $output->writeln("Longitude: $longitude, Latitude: $latitude");

        $speed = $this->windProvider->getDirection($date, $longitude, $latitude);
        $output->writeln('Direction: ' . $speed);

        $speed = $this->windProvider->getSpeed($date, $longitude, $latitude);
        $output->writeln('Speed: ' . $speed);

        /*$date = new DateTime();
        $date->setTime(intval($date->format('G') / 3) * 3, 0);

        $path = sprintf(
            '%s/var/wind/%s/%s/%s-ds.wind',
            $this->projectDir,
            $date->format('Ymd'),
            $date->format('H'),
            '1p00'
        );

        $handle = fopen($path, 'rb');
        $binary = fread($handle, filesize($path));
        fclose($handle);

        $data = array_values(unpack('S*', $binary));

        $output->writeln('Count: ' . count($data));
        $output->writeln(var_export(array_slice($data, 0, 10), true));*/

        return 1;
    }

    private function write(OutputInterface $output): void
    {
        $data = [
            1234,
            9876,
            4612,
            7534
        ];

        $bytes = pack('SS', $data[0], $data[1]);
        $bytes .= pack('SS', $data[2], $data[3]);

        $output->writeln("Length: " . strlen($bytes));

        $path = sprintf('%s/var/test.bin', $this->projectDir);

        $handle = fopen($path, 'wb');

        for ($length = 0; $length < strlen($bytes); $length += $tmp) {
            $tmp = fwrite($handle, substr($bytes, $length));
            if ($tmp === false) {
                throw new \RuntimeException("Failed to write into $path file.");
            }
        }

        fclose($handle);
    }

    private function read(OutputInterface $output): void
    {
        $path = sprintf('%s/var/test.bin', $this->projectDir);

//        $filesize = filesize($path);
//        $output->writeln("Filesize: " . $filesize);

        $handle = fopen($path, 'rb');
        fseek($handle, 4);
        $binary = fread($handle, 4);
        fclose($handle);

        $data = array_values(unpack('S*', $binary));

        $output->writeln(var_export($data, true));
    }
}
