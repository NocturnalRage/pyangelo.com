<?php
namespace PyAngelo\Repositories;

class MysqlCountryRepository implements CountryRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getCountries() {
    $sql = "SELECT country_code, country_name, currency_code
	          FROM   country";
    $result = $this->dbh->query($sql);
    $countries = $result->fetch_all(MYSQLI_ASSOC);
    return $countries;
  }

  public function getRealCountries() {
    $sql = "SELECT country_code, country_name, currency_code
            FROM   country
            WHERE  country_code not in ('A1', 'A2')
            ORDER by country_name";
    $result = $this->dbh->query($sql);
    $countries = $result->fetch_all(MYSQLI_ASSOC);
    return $countries;
  }

  public function getCountry($countryCode) {
    $sql = "SELECT country_code, country_name, currency_code
	          FROM   country
            WHERE  country_code = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $countryCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $country = $result->fetch_assoc();
    return $country;
  }

  public function getCurrencyFromCountryCode($countryCode) {
    $sql = "SELECT cur.*
            FROM   country c
            JOIN   currency cur on c.currency_code = cur.currency_code
            WHERE  country_code = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $countryCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }
}
