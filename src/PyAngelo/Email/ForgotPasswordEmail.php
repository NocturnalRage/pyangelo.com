<?php
namespace PyAngelo\Email;

class ForgotPasswordEmail extends EmailMessage {

  protected function prepareEmail(array $mailInfo) {
    $resetLink = $mailInfo['requestScheme'] . '://' .
      $mailInfo['serverName'] .
      '/reset-password?token=' .
      $mailInfo['token'];
    $toEmail = $mailInfo['toEmail'];
    $subject = "Password Reset Request | PyAngelo";

    // Now, put together the body of the email
    $bodyText = "Hi " . htmlspecialchars($mailInfo['givenName'], ENT_QUOTES, 'UTF-8') . ",\n" .
            "\nTo reset your PyAngelo password click on the following link:" .
            "\n\n$resetLink" .
            "\n\nThis link will remain valid for 24 hours and can be used once only." .
            "\n\nKind regards,\nJeff Plumb.";

    $bodyTextHtml = "Hi " . htmlspecialchars($mailInfo['givenName'], ENT_QUOTES, 'UTF-8') . ",<br /><br />To reset your PyAngelo password click on the following button:";

    $bodyHtml = $this->emailTemplate->addEmailHeader('Password Reset Request | PyAngelo');
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailButton('Reset Your Password', $resetLink);
    $bodyHtml .= $this->emailTemplate->addEmailParagraph('You can also copy the following link into your browser to reset your password:');
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($resetLink);
    $bodyHtml .= $this->emailTemplate->addEmailParagraph("This link will remain valid for 24 hours and can be used once only.");
    $bodyHtml .= $this->emailTemplate->addEmailParagraph('Regards,<br />Jeff Plumb.');
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the PyAngelo website');

    $this->setToEmail($toEmail);
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }
}
?>
