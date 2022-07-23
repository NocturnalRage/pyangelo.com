<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlCountryRepository;
use Tests\Factory\TestData;

class MysqlCountryRepositoryTest extends TestCase {
  protected $dbh;
  protected $countryRepository;
  protected $testData;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->dbh->begin_transaction();
    $this->countryRepository = new MysqlCountryRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->rollback();
    $this->dbh->close();
  }

  public function testInsertRetrieveDeleteCountry() {
    $this->testData->createCountry(
      'AU', 'Australia', 'AUD'
    );
    $this->testData->createCountry(
      'A1', 'Anonymous Proxy', 'USD'
    );
    $this->testData->createCurrency(
      'AUD',
      'Australian Dollar',
      '$',
      100
    );
    $countries = $this->countryRepository->getCountries();
    $expectedCountries = [
      [
        'country_code' => 'A1',
        'country_name' => 'Anonymous Proxy',
        'currency_code' => 'USD'
      ],
      [
        'country_code' => 'AU',
        'country_name' => 'Australia',
        'currency_code' => 'AUD'
      ],
    ];
    $this->assertEquals($expectedCountries, $countries);

    $expectedRealCountries = [
      [
        'country_code' => 'AU',
        'country_name' => 'Australia',
        'currency_code' => 'AUD'
      ],
    ];
    $realCountries = $this->countryRepository->getRealCountries();
    $this->assertSame($expectedRealCountries, $realCountries);

    $countryCode = 'NoSuchCountryCode';
    $country = $this->countryRepository->getCountry($countryCode);
    $this->assertNull($country);

    $countryCode = 'AU';
    $country = $this->countryRepository->getCountry($countryCode);
    $expectedCountryCode = 'AU';
    $expectedCountryName = 'Australia';
    $expectedCurrencyCode = 'AUD';
    $this->assertEquals($expectedCountryCode, $country['country_code']);
    $this->assertEquals($expectedCountryName, $country['country_name']);
    $this->assertEquals($expectedCurrencyCode, $country['currency_code']);

    $currency = $this->countryRepository->getCurrencyFromCountryCode(
      $countryCode
    );
    $this->assertEquals('AUD', $currency['currency_code']);
    $this->assertEquals('Australian Dollar', $currency['currency_description']);
    $this->assertEquals('$', $currency['currency_symbol']);
    $this->assertEquals(100, $currency['stripe_divisor']);
  }
}
