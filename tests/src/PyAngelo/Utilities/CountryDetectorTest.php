<?php
namespace Tests\src\PyAngelo\Utilities;

use PHPUnit\Framework\TestCase;
use Framework\Request;
use Dotenv\Dotenv;
use GeoIp2\Database\Reader; 
use PyAngelo\Utilities\CountryDetector;

class CountryDetectorTest extends TestCase {
  public function testGetCountryFromIp() {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $reader = new Reader($_ENV['GEOIP2_COUNTRY_DB']);
    $request = new Request($GLOBALS);
    $countryDetector = New CountryDetector($request, $reader);
    $countryCode = $countryDetector->getCountryFromIp();
    $this->assertSame('O1', $countryCode);

    $request->server['REMOTE_ADDR'] = '172.105.168.33';
    $countryCode = $countryDetector->getCountryFromIp();
    $this->assertSame('US', $countryCode);
  }
}
?>
