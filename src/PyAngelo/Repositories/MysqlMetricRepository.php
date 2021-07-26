<?php
namespace PyAngelo\Repositories;

class MysqlMetricRepository implements MetricRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getSubscriberGrowthByMonth() {
    $sql = "SELECT startmonth, ordermonth
                  , sum(subscribed) subscribed
                  , sum(cancelled) cancelled
                  , sum(subscribed) - sum(cancelled) net
            FROM (
              SELECT DATE_FORMAT(s.start_date, '%b %Y') startmonth,
                     DATE_FORMAT(s.start_date, '%Y%m') ordermonth,
                     1 as subscribed,
                     0 as cancelled
              FROM   stripe_subscription s
              UNION ALL
              SELECT DATE_FORMAT(s.canceled_at, '%b %Y') startmonth,
                     DATE_FORMAT(s.canceled_at, '%Y%m') ordermonth,
                     0 as subscribed,
                     1 as cancelled
              FROM   stripe_subscription s
              WHERE  canceled_at is not null
            ) stats
            GROUP BY startmonth, ordermonth
            ORDER BY ordermonth";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getSubscriberPaymentsByMonth() {
    $sql = "SELECT DATE_FORMAT(sp.paid_at, '%b %Y') startmonth,
                   DATE_FORMAT(sp.paid_at, '%Y%m') ordermonth,
                   SUM(net_aud_in_cents)/100 pyangelo,
                   SUM(stripe_fee_aud_in_cents)/100 stripe,
                   SUM(tax_fee_aud_in_cents)/100 tax
            FROM   stripe_subscription_payment sp
            GROUP BY startmonth, ordermonth
            ORDER BY ordermonth";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getPremiumMemberCountByMonth() {
    $sql = "SELECT DATE_FORMAT(months.month, '%b %Y') month,
                   SUM(case when months.month between s.start_date and s.current_period_end then 1 else 0 end) premium_member_count
            FROM   stripe_subscription s
            CROSS JOIN (
              SELECT now() month
              UNION ALL
              SELECT last_day(now() - INTERVAL 1 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 2 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 3 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 4 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 5 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 6 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 7 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 8 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 9 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 10 MONTH) month
              UNION ALL
              SELECT last_day(now() - INTERVAL 11 MONTH) month
            ) months
            GROUP BY months.month";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getPremiumMemberCountByPlan() {
    $sql = "SELECT sprod.product_name,
                   sprod.product_description,
                   COUNT(*) count
            FROM   stripe_subscription s
            JOIN   stripe_price sp on sp.stripe_price_id = s.stripe_price_id
            JOIN   stripe_product sprod on sprod.stripe_product_id = sp.stripe_product_id
            WHERE  s.status = 'active'
            AND    s.canceled_at IS NULL
            GROUP BY sprod.product_name, sprod.product_description";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getPremiumMemberCountByCountry() {
    $sql = "SELECT c.country_name,
                   count(*) count
            FROM   stripe_subscription s
            JOIN   person p on p.person_id = s.person_id
            JOIN   country c on c.country_code = p.country_code
            WHERE  p.active = TRUE
            AND    s.status = 'active'
            AND    s.canceled_at IS NULL
            GROUP BY c.country_name
            ORDER by count(*) DESC";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getMemberCountByMonth() {
    $sql = "SELECT DATE_FORMAT(created_at, '%Y%m') cym,
                   DATE_FORMAT(created_at, '%b %Y') month,
                   COUNT(*) count
            FROM   person
            WHERE  active = TRUE
            AND    created_at >= DATE_ADD(LAST_DAY(DATE_SUB(CURDATE(), interval 13 month)), interval 1 day)
            GROUP BY month, cym
            ORDER BY cym";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getMemberCountByDay() {
    $sql = "SELECT DATE(created_at) created_at,
                   COUNT(*) count
            FROM   person
            WHERE  active = TRUE
            AND    created_at >= curdate() - INTERVAL 28 DAY
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at)";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getMemberCountByCountry() {
    $sql = "SELECT c.country_name,
                   count(*) count
            FROM   person p
            JOIN   country c on c.country_code = p.country_code
            WHERE  active = TRUE
            GROUP BY c.country_name
            ORDER by count(*) DESC";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getCountMetrics() {
    $sql = "SELECT sum(total_members) total_members
                  , sum(premium_members) premium_members
                  , sum(past_due) past_due
            FROM (
              SELECT COUNT(*) total_members,
                     0 premium_members,
                     0 past_due
              FROM   person
              WHERE  active = 1
              UNION ALL
              SELECT 0 total_members,
                     COUNT(*) premium_members,
                     0 past_due
              FROM   stripe_subscription s
              WHERE  s.status = 'active'
              AND    s.canceled_at IS NULL
              UNION ALL
              SELECT 0 total_members,
                     0 premium_members,
                     COUNT(*) past_due
              FROM   stripe_subscription s
              WHERE  status = 'past_due'
              AND    canceled_at is null
            ) stats";

    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }
}
