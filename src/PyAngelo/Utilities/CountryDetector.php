<?php
namespace PyAngelo\Utilities;

use Framework\Request;
use GeoIp2\Database\Reader; 

class CountryDetector {
  protected $request;
  protected $geoReader;

  public function __construct(
    Request $request,
    Reader $geoReader
  ) {
    $this->request = $request;
    $this->geoReader = $geoReader;
  }

  public function getCountryFromIp() {
    try {
      $ip = $this->request->server['REMOTE_ADDR'] ?? '127.0.0.1';
      $record = $this->geoReader->country($ip);
      $detectedCountryCode = $record->country->isoCode;
    }
    catch (\Exception | \TypeError $e) {
      $detectedCountryCode = 'O1';
    }
    return $detectedCountryCode;
  }
}
?>
