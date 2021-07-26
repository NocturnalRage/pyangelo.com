<?php
namespace PyAngelo\Email;

class WhyCancelEmail extends EmailMessage {

  protected function prepareEmail(array $mailInfo) {
    $toEmail = $mailInfo['toEmail'];
    $subject = "I'd Appreciate Your Feedback";

    // Now, put together the body of the email
    $bodyText = "Hi " . htmlspecialchars($mailInfo['givenName'], ENT_QUOTES, 'UTF-8') . ",\n" .
            "\nI noticed you cancelled your PyAngelo premium membership today. If you could spare a few minutes, I'd really appreciate if you sent us a quick email to let us know why, and whether there's anything we could do better here at PyAngelo." .
            "\n\nCheers,\nThe PyAngelo Team.";

    $bodyTextHtml = "Hi " . htmlspecialchars($mailInfo['givenName'], ENT_QUOTES, 'UTF-8') . ",<br /><br />I noticed you cancelled your PyAngelo premium membership today. If you could spare a few minutes, I'd really appreciate if you sent us a quick email to let us know why, and whether there's anything we could do better here at PyAngelo.";

    $bodyHtml = $this->emailTemplate->addEmailHeader("I'd Appreciate Your Feedback");
    $bodyHtml .= $this->emailTemplate->addEmailBodyStart();
    $bodyHtml .= $this->emailTemplate->addEmailParagraph($bodyTextHtml);
    $bodyHtml .= $this->emailTemplate->addEmailParagraph('Cheers,<br />The PyAngelo Team.');
    $bodyHtml .= $this->emailTemplate->addEmailBodyEnd();
    $bodyHtml .= $this->emailTemplate->addEmailFooterMessage('Thanks for being a premium member and supporting PyAngelo');

    $this->setToEmail($toEmail);
    $this->setSubject($subject);
    $this->setBodyText($bodyText);
    $this->setBodyHtml($bodyHtml);
  }
}
?>
