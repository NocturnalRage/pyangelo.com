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
      $record = $this->geoReader->country($this->request->server['REMOTE_ADDR']);
      $detectedCountryCode = $record->country->isoCode;
    }
    catch (\Exception $e) {
      $detectedCountryCode = 'O1';
    }
    return $detectedCountryCode;
  }
}
?>
