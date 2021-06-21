<?php
namespace PyAngelo\Email;

class ContactUsEmail extends EmailMessage {

  protected function prepareEmail(array $mailInfo) {
    $replyEmail = $mailInfo['replyEmail'];
    $subject = $mailInfo['name'] . ' has contacted us';

    // Now, put together the body of the email
    $bodyText = htmlspecialchars($mailInfo['inquiry'], ENT_QUOTES, 'UTF-8') . "\n" .
            "\n\nThis email was generated from the contact page on the PyAngelo website.";

    $bodyTextHtml = htmlspecialchars($mailInfo['inquiry'], ENT_QUOTES, 'UTF-8');

    $bodyHtml = $this->emailTemplate->addEmailHeader('Contact Us | PyAngelo');
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('This email was generated from the contact page on the PyAngelo website.');

    $this->setToEmail('jeff@nocturnalrage.com');
    $this->setReplyEmail($replyEmail);
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }
}
?>
