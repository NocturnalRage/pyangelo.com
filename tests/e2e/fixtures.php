<?php
/**
 * Template data for the pages the Playwright smoke tests render. Each entry
 * returns [view, vars], in the same shape the view tests in tests/views use.
 */

$loggedIn = [
  'loggedIn' => true,
  'details' => ['person_id' => 99, 'given_name' => 'Fred'],
  'isAdmin' => false,
  'isImpersonating' => false,
  'crsfToken' => 'dummy-crsf-token',
  'unreadNotificationCount' => 0
];
$admin = array_merge($loggedIn, ['isAdmin' => true]);
$page = fn (string $title) => [
  'pageTitle' => $title,
  'metaDescription' => $title,
  'activeLink' => 'Home'
];

return [
  'home' => fn () => ['home.html.php', $page('PyAngelo') + [
    'personInfo' => array_merge($loggedIn, ['loggedIn' => false])
  ]],

  'ask' => fn () => ['ask-the-teacher/ask.html.php', $page('Ask a question') + [
    'personInfo' => $loggedIn
  ]],

  'blog-show' => fn () => ['blog/show.html.php', $page('PyAngelo Blog') + [
    'personInfo' => $loggedIn,
    'blog' => [
      'blog_id' => 1,
      'title' => 'Blog 1',
      'slug' => 'blog-1',
      'content' => '<p>Content 1</p>',
      'preview' => 'Preview 1',
      'category_description' => 'News',
      'category_slug' => 'news',
      'published_at' => '2026-01-01 12:30:00'
    ],
    'alertUser' => false,
    'purifier' => new Framework\Presentation\HtmlPurifierPurify(new HTMLPurifier()),
    'avatar' => new Framework\Presentation\Gravatar(25),
    'comments' => []
  ]],

  'lesson-sort' => fn () => ['lessons/sort.html.php', $page('Sort PyAngelo Lessons') + [
    'personInfo' => $admin,
    'tutorial' => ['title' => 'Tutorial 1', 'slug' => 'tutorial-1', 'display_order' => 1, 'thumbnail' => 't.jpg'],
    'lessons' => [
      ['lesson_title' => 'Lesson 1', 'lesson_slug' => 'lesson-1', 'display_order' => 1],
      ['lesson_title' => 'Lesson 2', 'lesson_slug' => 'lesson-2', 'display_order' => 2],
      ['lesson_title' => 'Lesson 3', 'lesson_slug' => 'lesson-3', 'display_order' => 3]
    ]
  ]],

  'subscription-payment-form' => fn () => ['membership/subscription-payment-form.html.php', $page('Become a PyAngelo Premium Member') + [
    'personInfo' => $loggedIn,
    'pyangeloPrice' => [
      'product_name' => 'Full Access',
      'product_description' => 'Full Access Description',
      'currency_code' => 'AUD',
      'currency_description' => 'Australian Dollars',
      'stripe_divisor' => 100
    ],
    'stripePrice' => (object) ['id' => 'price_fixture', 'unit_amount' => 995],
    'stripePublishableKey' => 'pk_test_fixture',
    'clientSecret' => 'fixture-client-secret',
    'numberFormatter' => new NumberFormatter('en_AU', NumberFormatter::CURRENCY)
  ]],

  'payment-method' => fn () => ['profile/payment-method.html.php', $page('Update Your Credit Card Details') + [
    'personInfo' => $loggedIn,
    'hasActiveSubscription' => true,
    'person' => ['last4' => '4242', 'email' => 'fixture@example.test', 'given_name' => 'Fred'],
    'stripePublishableKey' => 'pk_test_fixture'
  ]],

  'quiz' => fn () => ['quizzes/show.html.php', $page('PyAngelo Quiz') + [
    'personInfo' => $loggedIn,
    'tutorial' => ['title' => 'Tutorial 1', 'slug' => 'tutorial-1'],
    'quizInfo' => ['quiz_id' => 7, 'question_count' => 1]
  ]],

  'playground' => fn () => ['sketch/playground.html.php', $page('PyAngelo Playground') + [
    'personInfo' => $loggedIn,
    'sketch' => ['sketch_id' => 0, 'person_id' => 0, 'layout' => 'cols']
  ]]
];
