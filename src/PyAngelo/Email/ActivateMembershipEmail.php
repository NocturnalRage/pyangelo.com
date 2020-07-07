<?php
namespace PyAngelo\Email;

class ActivateMembershipEmail extends EmailMessage {

  protected function prepareEmail(array $mailInfo) {
    $activateLink = $mailInfo['requestScheme'] . '://' .
      $mailInfo['serverName'] .
      '/activate-free-membership?token=' .
      $mailInfo['token'];
    $toEmail = $mailInfo['toEmail'];
    $subject = "Activate Your Free PyAngelo Membership";

    // Now, put together the body of the email
    $bodyText = "Hi " . htmlspecialchars($mailInfo['givenName'], ENT_QUOTES, 'UTF-8') . ",\n" .
            "\nPlease click on the following link to activate your free PyAngelo membership. As soon as you do you'll have access to all our videos, be able to track your progress through our tutorials, and be able to start coding." .
            "\n\n$activateLink" .
            "\n\nKind regards,\nJeff Plumb.";

    $bodyTextHtml = "Hi " . htmlspecialchars($mailInfo['givenName'], ENT_QUOTES, 'UTF-8') . ",<br /><br />Please click on the following button to activate your free PyAngelo membership. As soon as you do you'll have access to all our videos, be able to track your progress through our tutorials, and be able to start coding.";

    $bodyHtml = $this->emailTemplate->addEmailHeader('Activate Your Free PyAngelo Membership');
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailButton('Activate Your Membership', $activateLink);
    $bodyHtml .= $this->emailTemplate->addEmailParagraph('You can also copy the following into your browser to activate your free membership:');
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($activateLink);
    $bodyHtml .= $this->emailTemplate->addEmailParagraph('Regards,<br />Jeff Plumb.');
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo website.');

    $this->setToEmail($toEmail);
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }
}
?>
