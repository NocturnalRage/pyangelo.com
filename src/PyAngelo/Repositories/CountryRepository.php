<?php
namespace PyAngelo\Repositories;

interface CountryRepository {

  public function getCountries();

  public function getRealCountries();

  public function getCountry($countryCode);

  public function getCurrencyFromCountryCode($countryCode);
}
