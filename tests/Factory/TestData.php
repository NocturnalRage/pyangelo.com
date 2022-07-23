<?php
namespace Tests\Factory;

class TestData {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function createCountry(
    $countryCode,
    $countryName,
    $currencyCode
  ) {
    $sql = "INSERT INTO country (
              country_code,
              country_name,
              currency_code
            )
            VALUES (?, ?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sss',
      $countryCode,
      $countryName,
      $currencyCode
    );
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function createCurrency(
    $currencyCode,
    $currencyDescription,
    $currencySymbol,
    $stripeDivisor
  ) {
    $sql = "INSERT INTO currency (
              currency_code,
              currency_description,
              currency_symbol,
              stripe_divisor
            )
            VALUES (?, ?, ?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sssi',
      $currencyCode,
      $currencyDescription,
      $currencySymbol,
      $stripeDivisor
    );
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function createPerson($personId, $email) {
    $sql = "INSERT INTO person (
      person_id,
      given_name,
      family_name,
      email,
      password,
      email_status_id,
      bounce_count,
      active,
      country_code,
      detected_country_code,
      last_login,
      created_at,
      updated_at
    )
    values (
      $personId,
      'Fast',
      'Fred',
      '$email',
      'secret',
      1,
      0,
      1,
      'US',
      'US',
      now(),
      now(),
      now()
    )";
    $result = $this->dbh->query($sql);
  }

  public function createPrice($priceId, $productId) {
    $sql = "INSERT INTO stripe_product values ('$productId', 'Test Subscription', 'Test subscription for PyAngelo', 1)";
    $result = $this->dbh->query($sql);
    $sql = "INSERT INTO stripe_price values ('$priceId', '$productId', 'USD', 695, 'month', 1)";
    $result = $this->dbh->query($sql);
  }

  public function createBlogCategory($blogCategoryId) {
    $sql = "INSERT INTO blog_category values ($blogCategoryId, 'Coding Advice')";
    $result = $this->dbh->query($sql);
  }

  public function createBlog($title, $slug, $blogCategoryId, $personId, $featured = 0) {
    $sql = "INSERT INTO blog values (
      NULL,
      $personId,
      '$title',
      'Blog intro.',
      'My test blog post.',
      '$slug',
      'blog-image.jpg',
      $blogCategoryId,
      $featured,
      1,
      now(),
      now(),
      now()
    )";
    $result = $this->dbh->query($sql);
  }

  public function createBlogImage($blogImageId) {
    $sql = "INSERT INTO blog_image values (
      $blogImageId,
      'test-image.png',
      640,
      360,
      now()
    )";
    $result = $this->dbh->query($sql);
  }

  public function createSubscriptions($price) {
    $sql = "INSERT INTO stripe_subscription values (
      'SUB-1',
      1,
      0,
      NULL,
      '2017-01-01',
      '2017-02-01',
      'CUS-1',
      '$price',
      'SECRET',
      '2017-01-01',
      'active',
      0,
      now(),
      now()
    )";
    $result = $this->dbh->query($sql);
    $sql = "INSERT INTO stripe_subscription values (
      'SUB-2',
      1,
      0,
      '2016-12-05',
      '2016-12-01',
      '2017-01-01',
      'CUS-2',
      '$price',
      'SECRET',
      '2016-12-01',
      'canceled',
      0,
      now(),
      now()
    )";
    $result = $this->dbh->query($sql);
    $sql = "INSERT INTO stripe_subscription values (
      'SUB-3',
      1,
      0,
      '2016-12-31',
      '2016-12-16',
      '2017-01-16',
      'CUS-3',
      '$price',
      'SECRET',
      '2016-12-21',
      'canceled',
      0,
      now(),
      now()
    )";
    $result = $this->dbh->query($sql);
    $sql = "INSERT INTO stripe_subscription values (
      'SUB-4',
      1,
      0,
      NULl,
      '2016-12-16',
      '2017-01-16',
      'CUS-4',
      '$price',
      'SECRET',
      '2016-12-16',
      'incomplete',
      0,
      now(),
      now()
    )";
    $sql = "INSERT INTO stripe_subscription values (
      'SUB-5',
      1,
      0,
      NULl,
      '2016-12-16',
      '2017-01-16',
      'CUS-4',
      '$price',
      'SECRET',
      '2016-12-16',
      'incomplete_expired',
      0,
      now(),
      now()
    )";
    $result = $this->dbh->query($sql);
  }

  public function createSubscriberPayments($price) {
    $sql = "INSERT INTO stripe_subscription values (
      'SUB-1',
      1,
      0,
      NULL,
      '2017-01-01',
      '2017-02-01',
      'CUS-1',
      '$price',
      'SECRET',
      '2017-01-01',
      'active',
      0,
      now(),
      now()
    )";
    $result = $this->dbh->query($sql);
    $sql = "INSERT INTO stripe_subscription_payment values (
      'SUB-1',
      1,
      'USD',
      1000,
      '2017-01-01',
      10,
      90,
      600,
      'CHG-1',
      NULL,
      NULL
    )";
    $result = $this->dbh->query($sql);
  }

  public function createQuestionType($questionTypeId) {
    $sql = "INSERT INTO question_type values ($questionTypeId, 'PyAngelo', 'PyAngelo')";
    $result = $this->dbh->query($sql);
  }

  public function createSkill($skillName, $slug) {
    $sql = "INSERT INTO skill (skill_id, skill_name, slug, created_at, updated_at) values (NULL, '$skillName', '$slug', now(), now())";
    $result = $this->dbh->query($sql);
  }
}
?>
