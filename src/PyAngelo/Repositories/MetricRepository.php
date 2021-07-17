<?php
namespace PyAngelo\Repositories;

interface MetricRepository {

  public function getSubscriberGrowthByMonth();

  public function getSubscriberPaymentsByMonth();

  public function getPremiumMemberCountByMonth();

  public function getPremiumMemberCountByPlan();

  public function getPremiumMemberCountByCountry();

  public function getMemberCountByMonth();

  public function getMemberCountByDay();

  public function getMemberCountByCountry();

  public function getCountMetrics();
}
