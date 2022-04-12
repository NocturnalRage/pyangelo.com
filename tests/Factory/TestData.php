<?php
namespace Tests\Factory;

class TestData {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function deleteAllCountries() {
    $sql = "DELETE FROM country";
    $result = $this->dbh->query($sql);
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

  public function deleteAllPeople() {
    $this->deleteAllQuestions();
    $this->deleteAllCampaignActivity();
    $this->deleteAllSubscriptions();
    $this->deleteAllBlogs();
    $sql = "DELETE FROM membership_activate";
    $result = $this->dbh->query($sql);
    $sql = "DELETE FROM password_reset_request";
    $result = $this->dbh->query($sql);
    $sql = "DELETE FROM subscriber";
    $result = $this->dbh->query($sql);
    $sql = "DELETE FROM notification";
    $result = $this->dbh->query($sql);
    $sql = "DELETE FROM person";
    $result = $this->dbh->query($sql);
  }

  public function createPerson($personId, $email) {
    $this->deleteAllPeople();
    $sql = "DELETE FROM country";
    $result = $this->dbh->query($sql);
    $sql = "INSERT INTO country values ('US', 'United States', 'USD')";
    $result = $this->dbh->query($sql);
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

  public function deleteAllCampaignActivity() {
    $sql = "DELETE FROM campaign_activity";
    $result = $this->dbh->query($sql);
  }

  public function deleteAllSubscriptions() {
    $sql = "DELETE FROM stripe_subscription_payment";
    $result = $this->dbh->query($sql);
    $sql = "DELETE FROM stripe_subscription";
    $result = $this->dbh->query($sql);
  }

  public function createPrice($priceId, $productId) {
    $sql = "DELETE FROM stripe_price";
    $result = $this->dbh->query($sql);
    $sql = "DELETE FROM stripe_product";
    $result = $this->dbh->query($sql);
    $sql = "INSERT INTO stripe_product values ('$productId', 'Test Subscription', 'Test subscription for PyAngelo', 1)";
    $result = $this->dbh->query($sql);
    $sql = "INSERT INTO stripe_price values ('$priceId', '$productId', 'USD', 695, 'month', 1)";
    $result = $this->dbh->query($sql);
  }

  public function deleteAllStripeEvents() {
    $sql = "DELETE FROM stripe_event";
    $result = $this->dbh->query($sql);
  }

  public function deleteAllBlogComments() {
    $sql = "DELETE FROM blog_comment";
    $result = $this->dbh->query($sql);
  }

  public function deleteAllBlogs() {
    $this->deleteAllBlogComments();
    $sql = "DELETE FROM blog";
    $result = $this->dbh->query($sql);
  }

  public function deleteAllBlogCategories() {
    $sql = "DELETE FROM blog_category";
    $result = $this->dbh->query($sql);
  }

  public function createBlogCategory($blogCategoryId) {
    $sql = "INSERT INTO blog_category values ($blogCategoryId, 'Coding Advice')";
    $result = $this->dbh->query($sql);
  }

  public function createBlog($title, $slug, $featured = 0) {
    $this->deleteAllBlogs();
    $this->deleteAllPeople();
    $this->deleteAllBlogCategories();
    $personId = 1;
    $blogCategoryId = 1;
    $this->createPerson($personId, 'admin@nocturnalrage.com');
    $this->createBlogCategory($blogCategoryId);
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

  public function deleteAllBlogImages() {
    $sql = "DELETE FROM blog_image";
    $result = $this->dbh->query($sql);
  }

  public function createBlogImage($blogImageId) {
    $this->deleteAllBlogImages();
    $sql = "INSERT INTO blog_image values (
      $blogImageId,
      'test-image.png',
      640,
      360,
      now()
    )";
    $result = $this->dbh->query($sql);
  }

  public function createSubscribers() {
    $this->deleteAllSubscriptions();
    $this->deleteAllPeople();
    $this->createPrice('Price1', 'Monthly');
    $this->createPerson(1, 'fastfred@hotmail.com');
    $sql = "INSERT INTO stripe_subscription values (
      'SUB-1',
      1,
      0,
      NULL,
      '2017-01-01',
      '2017-02-01',
      'CUS-1',
      'Price1',
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
      'Price1',
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
      'Price1',
      'SECRET',
      '2016-12-21',
      'canceled',
      0,
      now(),
      now()
    )";
    $result = $this->dbh->query($sql);
  }

  public function createSubscriberPayments() {
    $this->deleteAllSubscriptions();
    $this->deleteAllPeople();
    $this->createPrice('Price1', 'Monthly');
    $this->createPerson(1, 'fastfred@hotmail.com');
    $sql = "INSERT INTO stripe_subscription values (
      'SUB-1',
      1,
      0,
      NULL,
      '2017-01-01',
      '2017-02-01',
      'CUS-1',
      'Price1',
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
      'AUD',
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

  public function deleteAllNotifications() {
    $sql = "DELETE FROM  notification";
    $result = $this->dbh->query($sql);
  }

  public function deleteAllQuestionAlerts() {
    $sql = "DELETE FROM question_alert";
    $result = $this->dbh->query($sql);
  }

  public function deleteAllQuestionComments() {
    $sql = "DELETE FROM question_comment";
    $result = $this->dbh->query($sql);
  }

  public function deleteAllQuestions() {
    $this->deleteAllQuestionComments();
    $this->deleteAllQuestionAlerts();
    $sql = "DELETE FROM question";
    $result = $this->dbh->query($sql);
  }
  public function deleteAllQuestionTypes() {
    $sql = "DELETE FROM question_type";
    $result = $this->dbh->query($sql);
  }

  public function createQuestionType($questionTypeId) {
    $sql = "INSERT INTO question_type values ($questionTypeId, 'PyAngelo', 'PyAngelo')";
    $result = $this->dbh->query($sql);
  }
}
?>
