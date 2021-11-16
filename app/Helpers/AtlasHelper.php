<?php

namespace App\Helpers;

use Anam\PhantomMagick\Converter;

class AtlasHelper
{
    /**
     * @var string
     */
    public string $SCHEDULE_IMAGE_PATH;

    /**
     * @var string
     */
    public string $SCHEDULE_TABLE_STUB_PATH;

    /**
     * @var string
     */
    public string $PHANTOMJS_BIN_PATH;

    /**
     * @var string
     */
    public string $ATLAS_URL;

    /**
     * @var array
     */
    private array $NEEDED_TIMES;

    public function __construct()
    {
        $this->ATLAS_URL = strtr(getenv('ATLAS_URL'), ['%ATLAS_MONTH' => getenv('ATLAS_MONTH'), '%ATLAS_DAY' => getenv('ATLAS_DAY')]);
        $this->SCHEDULE_TABLE_STUB_PATH = dirname(__FILE__) . '/../Stubs/FilteredRidesTable.stub';
        $this->NEEDED_TIMES = explode(',', getenv('NEEDED_TIMES'));
        $this->SCHEDULE_IMAGE_PATH = dirname(__FILE__) . '/../../scheduleImage.png';
        $this->PHANTOMJS_BIN_PATH = dirname(__FILE__) . '/../../node_modules/phantomjs/lib/phantom/bin/phantomjs';
    }

    /**
     * Get filtered rides by needed time.
     *
     * @param object $data
     * @return array
     */
    public function getFilteredRides(object $data): array
    {
        $rides = self::getRides($data);
        return array_filter($rides, function ($ride) {
            foreach ($this->NEEDED_TIMES as $time) {
                if ($ride['freeSeats'] > 0 && strpos($ride['departure'], (string)$time) === 0) {
                    return true;
                }
            }
        });
    }

    /**
     * Deletes schedule image.
     */
    public function deleteScheduleImage()
    {
        unlink(realpath($this->SCHEDULE_IMAGE_PATH));
    }

    /**
     * Saves schedule image
     *
     * @param string $htmlRows
     * @throws \Exception
     */
    public function saveScheduleImage(string $htmlRows)
    {
        $conv = new Converter();
        $conv->setBinary($this->PHANTOMJS_BIN_PATH);
        $png = $conv->addPage($htmlRows)
            ->width(500)
            ->toPng();
        $png->save($this->SCHEDULE_IMAGE_PATH);
    }

    /**
     * Generates rows for schedule table.
     *
     * @param array $filteredRides
     * @return string
     */
    public function generateTableRows(array $filteredRides): string
    {
        $html = implode('', array_map(function ($ride) {
            return "<tr height='100px' style='text-align: center'><td style='border: 1px solid black'>" . $ride['freeSeats'] . "</td><td style='border: 1px solid black'>" . $ride['departure'] . "</td></tr>";
        }, $filteredRides));
        return str_replace(['{{filteredRides}}'], [$html], file_get_contents($this->SCHEDULE_TABLE_STUB_PATH));
    }

    /**
     * Get all rides.
     *
     * @param object $data
     * @return array
     */
    private static function getRides(object $data): array
    {
        return array_map(fn($ride) => ['freeSeats' => $ride->freeSeats, 'departure' => explode('T', $ride->pickupStops[0]->datetime)[1]], $data->rides);
    }
}
