<?php


namespace App\Services;


use App\Jobs\TryAddColumn;
use Illuminate\Support\Facades\Storage;

class AddUSDColumnService
{
    const RATE_API = 'https://api.exchangeratesapi.io/2020-10-28?base=RUB&symbo';
    private $storage, $exchangeAPI;

    public function __construct()
    {
        $this->storage = Storage::cloud();
        $this->exchangeAPI = json_decode(file_get_contents(self::RATE_API));
    }

    public function addUSDColumn()
    {
        $files = $this->storage->allFiles();
        $modifiedFiles = 0;
        foreach ($files as $file) {
            if ($this->storage->exists($file)) {

                $lastModified = $this->storage->lastModified($file);
                $lastModified = \DateTime::createFromFormat("U", $lastModified);
                $today = date('d');
                $lastModifiedDay = date('d', $lastModified->getTimestamp());

                if ($today === $lastModifiedDay) {
                    $modifiedFiles++;
                    $csvFile = $this->storage->get($file);
                    $csvFileLines = array_values(array_filter(preg_split('/[\r\n]/', $csvFile)));

                    if (count($csvFileLines) > 1) {
                        $isEmptyFile = true;
                        $rubIndex = 0;
                        foreach ($csvFileLines as $key => $csvFileLine) {
                            $csvFileLineItems = explode(',', $csvFileLine);
                            if ($key === 0) {
                                if (is_numeric(array_search('USD', $csvFileLineItems))) {
                                    break;
                                }
                                if ($isEmptyFile) {
                                    $this->storage->put($file, '');
                                    $isEmptyFile = false;
                                }
                                $rubIndex = array_search('RUB', $csvFileLineItems);
                                $csvFileLineItems[] = 'USD';
                                $this->storage->put($file, implode(',', $csvFileLineItems));
                            } else {
                                if (isset($csvFileLineItems[$rubIndex])) {
                                    $rub = $csvFileLineItems[$rubIndex];
                                    $usdCourse = round($this->exchangeAPI->rates->USD, 3);
                                    $usd = round($rub * $usdCourse, 2);
                                    $csvFileLineItems[] = $usd;
                                    $this->storage->append($file, implode(',', $csvFileLineItems));
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($modifiedFiles > 0) {
            return true;
        }
//      if the file was not uploaded yet, then try after 10 minutes till the file has been uploaded
        TryAddColumn::dispatch()->delay(now()->addMinutes(10));
        return true;
    }
}
