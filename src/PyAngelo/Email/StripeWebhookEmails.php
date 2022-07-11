<?php
namespace PyAngelo\Email;

use PyAngelo\Email\EmailTemplate;
use PyAngelo\Repositories\MailRepository;
use Framework\Contracts\MailContract;

class StripeWebhookEmails extends EmailMessage {

  protected function prepareEmail(array $mailInfo) {
    $method = $mailInfo['emailType'];
    if (method_exists($this, $method)) {
      $this->$method($mailInfo);
    }
  }

  private function invalidPayload($mailInfo) {
    $subject = 'Stripe Webhook Called With Invalid Payload';

    $bodyText = "Hey PyAngelo Team,\n\n " .
      "The Stripe webhook was called with an invalid payload.\n\n";

    $bodyTextHtml = "Hey PyAngelo Team,<br /><br />" .
      "The Stripe webhook was called with an invalid payload.";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo Stripe Webhook.');

    // Sending to ourselves
    $this->setToEmail($this->getFromEmail());
    $this->setFromEmail($this->getFromEmail());
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function invalidSignature($mailInfo) {
    $subject = 'Stripe Webhook Called With Invalid Signature';

    $bodyText = "Hey PyAngelo Team,\n\n " .
      "The Stripe webhook was called with an invalid signature.\n\n";

    $bodyTextHtml = "Hey PyAngelo Team,<br /><br />" .
      "The Stripe webhook was called with an invalid signature.";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo Stripe Webhook.');

    // Sending to ourselves
    $this->setToEmail($this->getFromEmail());
    $this->setFromEmail($this->getFromEmail());
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function noStripeEvent($mailInfo) {
    $subject = 'Stripe Webhook Called But Event Was Not from Stripe';

    $bodyText = "Hey PyAngelo Team,\n\n " .
      "The Stripe webhook was called but the event was not " .
      "verified by Stripe.\n\n" .
      "If you didn't call the webhook as a test then login to Stripe " .
      "and take a look at the event " . $mailInfo['stripeEventId'] .
      " to see if there were any issues.";

    $bodyTextHtml = "Hey PyAngelo Team,<br /><br />" .
      "The Stripe webhook was called but the event was not " .
      "verified by Stripe.<br /><br />" .
      "If you didn't call the webhook as a test then login to Stripe " .
      "and take a look at the event " . $mailInfo['stripeEventId'] .
      " to see if there were any issues.";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo Stripe Webhook.');

    // Sending to ourselves
    $this->setToEmail($this->getFromEmail());
    $this->setFromEmail($this->getFromEmail());
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function stripeEventAlreadyProcessed($mailInfo) {
    $subject = 'Stripe Webhook Sent a Duplicate Event';

    $bodyText = "Hey PyAngelo Team,\n\n " .
      "The Stripe webhook was called for an event we have already " .
      "processed.\n\n" .
      "If you didn't call the webhook as a test then login to Stripe " .
      "and take a look at the event " . $mailInfo['stripeEventId'] .
      " to see if there were any issues.";

    $bodyTextHtml = "Hey PyAngelo Team,<br /><br />" .
      "The Stripe webhook was called for an event we have already " .
      "processed.<br /><br />" .
      "If you didn't call the webhook as a test then login to Stripe " .
      "and take a look at the event " . $mailInfo['stripeEventId'] .
      " to see if there were any issues.";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo Stripe Webhook.');

    // Sending to ourselves
    $this->setToEmail($this->getFromEmail());
    $this->setFromEmail($this->getFromEmail());
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function unhandledWebhook($mailInfo) {
    $subject = 'PyAngelo Stripe Webhook Does Not Handle This Event';

    $bodyText = "Hey PyAngelo Team,\n\n " .
      "The Stripe webhook was called for an event that we are not " .
      "set up for.\n\n " .
      "If you didn't call the webhook as a test then login to Stripe " .
      "and take a look at the event type " . $mailInfo['stripeEventType'] .
      " to see if there were any issues.";

    $bodyTextHtml = "Hey PyAngelo Team,<br /><br />" .
      "The Stripe webhook was called for an event that we are not " .
      "set up for.<br /><br />" .
      "If you didn't call the webhook as a test then login to Stripe " .
      "and take a look at the event type " . $mailInfo['stripeEventType'] .
      " to see if there were any issues.";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo Stripe Webhook.');

    // Sending to ourselves
    $this->setToEmail($this->getFromEmail());
    $this->setFromEmail($this->getFromEmail());
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function noStripeSubscription($mailInfo) {
    $subject = 'Stripe Webhook Called But Subscription Does Not Exist';

    $bodyText = "Hey PyAngelo Team,\n\n " .
      "The Stripe webhook was called for a subscription that does not " .
      "exist\n\n " .
      "If you didn't call the webhook as a test then login to Stripe " .
      "to look at the subscription " . $mailInfo['stripeSubscriptionId'] .
      " to see if there were any issues.";

    $bodyTextHtml = "Hey PyAngelo Team,<br /><br /> " .
      "The Stripe webhook was called for a subscription that does not " .
      "exist.<br /><br />" .
      "If you didn't call the webhook as a test then login to Stripe " .
      "to look at the subscription " . $mailInfo['stripeSubscriptionId'] .
      " to see if there were any issues.";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo Stripe Webhook.');

    // Sending to ourselves
    $this->setToEmail($this->getFromEmail());
    $this->setFromEmail($this->getFromEmail());
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function couldNotRecordCharge($mailInfo) {
    $subject = 'Stripe Webhook Could Not Record Charge';

    $bodyText = "Hey PyAngelo Team,\n\n " .
      "The Stripe webhook could not record a charge " .
      "with the following id: " . $mailInfo['stripeChargeId'];

    $bodyTextHtml = "Hey PyAngelo Team,<br /><br /> " .
      "The Stripe webhook could not record a charge " .
      "with the following id: " . $mailInfo['stripeChargeId'];

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo Stripe Webhook.');

    // Sending to ourselves
    $this->setToEmail($this->getFromEmail());
    $this->setFromEmail($this->getFromEmail());
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function paymentReceived($mailInfo) {
    $subject = 'PyAngelo Premium Membership Payment';

    $bodyText = "Hey {$mailInfo['givenName']},\n\n " .
      "Thanks for your ongoing support of PyAngelo. We've just received " .
      "a payment of {$mailInfo['amountPaidMoney']} for " .
      "your monthly premium membership with PyAngelo. " .
      "If you have any questions about this payment then " .
      "please reply to this email.\n\n " .
      "Happy coding!\n" .
      "The PyAngelo Team.";

    $bodyTextHtml = "Hey {$mailInfo['givenName']},<br /><br />" .
      "Thanks for your ongoing support of PyAngelo. We've just received " .
      "a payment of {$mailInfo['amountPaidMoney']} for " .
      "your monthly premium membership with PyAngelo. " .
      "If you have any questions about this payment then " .
      "please reply to this email.<br /><br />" .
      "Happy coding!<br />" .
      "The PyAngelo Team.";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('We love coding!');

    $this->setToEmail($mailInfo['toEmail']);
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function paymentFailed($mailInfo) {
    $subject = 'PyAngelo Payment Failed';

    $bodyText = "Hey {$mailInfo['givenName']},\n\n " .
      "Your latest payment of {$mailInfo['amountDueMoney']} for " .
      "your PyAngelo premium membership has failed. " .
      "If this is because your credit card details have changed, you can " .
      "login to your account and update your details, then we will retry the " .
      "payment immediately.\n\n" .
      "If this is not the case you may need to ring your bank to authorise " .
      "the payment. We will automatically retry the payment " .
      "in {$mailInfo['retryPaymentDays']} days time." .
      "\n\n" .
      "If you have any questions or need any help with this then please let " .
      "me know. Thanks for your ongoing support.\n\n" .
      "Regards,\n" .
      "The PyAngelo Team.";

    $bodyTextHtml = "Hey {$mailInfo['givenName']},<br /><br />" .
      "Your latest payment of {$mailInfo['amountDueMoney']} for " .
      "your PyAngelo premium membership has failed. " .
      "If this is because your credit card details have changed, you can " .
      "login to your account and update your details, then we will retry the " .
      "payment immediately.<br /><br />" .
      "If this is not the case you may need to ring your bank to authorise " .
      "the payment. We will automatically retry the payment " .
      "in {$mailInfo['retryPaymentDays']} days time." .
      "<br /><br />" .
      "If you have any questions or need any help with this then please let " .
      "me know. Thanks for your ongoing support.<br /><br />" .
      "Regards,<br />" .
      "The PyAngelo Team.";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('We love coding!');

    $this->setToEmail($mailInfo['toEmail']);
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }

  private function pingWebhook($mailInfo) {
    $subject = 'Stripe Sent a Ping Webhook Event';

    $bodyText = "Hey PyAngelo Team,\n\n " .
      "Stripe sent a Ping webhook and I thought you'd like to know. ";
      "It was event " . $mailInfo['stripeEventId'] . ".";

    $bodyTextHtml = "Hey PyAngelo Team,<br /><br />" .
      "Stripe sent a Ping webhook and I thought you'd like to know. ";
      "It was event " . $mailInfo['stripeEventId'] . ".";

    $bodyHtml = $this->emailTemplate->addEmailHeader($subject);
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo Stripe Webhook.');

    // Sending to ourselves
    $this->setToEmail($this->getFromEmail());
    $this->setFromEmail($this->getFromEmail());
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }
}
?>
