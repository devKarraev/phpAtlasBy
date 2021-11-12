<?php

namespace App\Controllers;

use App\Helpers\AtlasHelper;
use App\Helpers\HttpHelper;
use TelegramBot\Api\Client;

class IndexController
{
    /**
     * @var Client
     */
    private Client $bot;

    /**
     * @var HttpHelper
     */
    private HttpHelper $httpHelper;

    /**
     * @var AtlasHelper
     */
    private AtlasHelper $atlasHelper;

    /**
     * @var string
     */
    private string $chatId;

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->bot = new Client($token);
        $this->httpHelper = new HttpHelper();
        $this->atlasHelper = new AtlasHelper();
        $this->chatId = getenv('RASUL_CHAT_ID');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute()
    {
        $data = $this->httpHelper->makeRequest($this->atlasHelper->ATLAS_URL);
        $filteredRides = $this->atlasHelper->getFilteredRides($data);
        $htmlRows = $this->atlasHelper->generateTableRows($filteredRides);

        try {
            $this->atlasHelper->saveScheduleImage($htmlRows);
        } catch (\Exception $exception) {
            return $this->bot->sendMessage($this->chatId, $exception->getMessage());
        }

        try {
            $this->bot->sendPhoto($this->chatId, new \CURLFile($this->atlasHelper->SCHEDULE_IMAGE_PATH));
            $this->atlasHelper->deleteScheduleImage();
        } catch (\Exception $exception) {
            return $this->bot->sendMessage($this->chatId, $exception->getMessage());
        }
    }
}
