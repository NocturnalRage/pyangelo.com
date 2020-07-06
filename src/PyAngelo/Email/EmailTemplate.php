<?php
namespace PyAngelo\Email;

class EmailTemplate {

  function addEmailHeader($title) {
   $emailHeader = <<<ENDHEADER
<!DOCTYPE html>
<html style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;">
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>$title</title>
  </head>
ENDHEADER;
     return $emailHeader;
  }

  function addEmailBodyStart() {
     $bodyStart = <<<ENDBODYSTART
<body bgcolor="#f6f6f6" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; margin: 0; padding: 0;">

<!-- body -->
<table class="body-wrap" bgcolor="#f6f6f6" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 20px;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"></td>
		<td class="container" bgcolor="#FFFFFF" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0;">
			<!-- content -->
			<div class="content" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; max-width: 600px; display: block; margin: 0 auto; padding: 0;">
			<table style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 0;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;">
ENDBODYSTART;
     return $bodyStart;
  }
  function addEmailParagraph($paragraphText) {
     $paragraph = <<<ENDP
						<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">$paragraphText</p>
ENDP;
     return $paragraph;
  }
  function addEmailH1($h1Text) {
     $h1 = <<<ENDH1
						<h1 style="font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 36px; line-height: 1.2; color: #000; font-weight: 200; margin: 40px 0 10px; padding: 0;">$h1Text</h1>
ENDH1;
     return $h1;
  }
  function addEmailH1Link($h1Text, $link) {
     $h1 = <<<ENDH1
						<h1 style="font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 36px; line-height: 1.2; color: #000; font-weight: 200; margin: 40px 0 10px; padding: 0;"><a href="$link" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; color: #348eda; margin: 0; padding: 0;">$h1Text</a></h1>
ENDH1;
     return $h1;
  }
  function addEmailH2($h2Text) {
     $h2 = <<<ENDH2
						<h2 style="font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 28px; line-height: 1.2; color: #000; font-weight: 200; margin: 40px 0 10px; padding: 0;">$h2Text</h2>
ENDH2;
     return $h2;
  }
  function addEmailH2Link($h2Text, $link) {
     $h2 = <<<ENDH2
						<h2 style="font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 28px; line-height: 1.2; color: #000; font-weight: 200; margin: 40px 0 10px; padding: 0;"><a href="$link" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; color: #348eda; margin: 0; padding: 0;">$h2Text</a></h2>
ENDH2;
     return $h2;
  }
  function addEmailImg($imgUrl, $altText, $imgLink) {
     $imgTxt = <<<ENDIMG
<table style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 0;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td class="padding" style="text-align: center; vertical-align: top; font-size: 0;">
									<a href="$imgLink" style="text-decoration: none; font-weight: bold; text-align: center;"><img src="$imgUrl" alt="$altText" style="border-style: none" /></a>
								</td>
							</tr></table>
ENDIMG;
     return $imgTxt;
  }
  function addEmailButton($buttonText, $link = "https://www.pyangelo.com", $buttonColour="#348eda") {
     $button = <<<ENDBUTTON
<table style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 0;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td class="padding" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 10px 0;">
									<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;"><a href="$link" class="btn-primary" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 2; color: #FFF; text-decoration: none; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 25px; background: $buttonColour; margin: 0 10px 0 0; padding: 0; border-color: $buttonColour; border-style: solid; border-width: 10px 20px;">$buttonText</a></p>
								</td>
							</tr></table>
ENDBUTTON;
     return $button;
  }
  function addEmailBodyEnd() {
     $bodyEnd = <<<ENDBODY
					</td>
				</tr></table></div>
			<!-- /content -->
		</td>
		<td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"></td>
	</tr></table><!-- /body -->
ENDBODY;
     return $bodyEnd;
  }
  function addEmailFooter() {
     $footer = <<<ENDEMAIL
<!-- footer --><table class="footer-wrap" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; clear: both !important; margin: 0; padding: 0;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"></td>
		<td class="container" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto; padding: 0;">
			<!-- content -->
			<div class="content" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; max-width: 600px; display: block; margin: 0 auto; padding: 0;">
				<table style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 0;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td align="center" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;">
							<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.6; color: #666; font-weight: normal; margin: 0 0 10px; padding: 0;">Don't like these annoying emails? <a href="#" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; color: #999; margin: 0; padding: 0;">Unsubscribe</a>.
							</p>
						</td>
					</tr></table></div>
			<!-- /content -->
		</td>
		<td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"></td>
	</tr></table><!-- /footer --></body>
</html>
ENDEMAIL;
     return $footer;
  }
  function addEmailFooterMessage($message) {
     $footer = <<<ENDEMAIL
<!-- footer --><table class="footer-wrap" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; clear: both !important; margin: 0; padding: 0;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"></td>
		<td class="container" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto; padding: 0;">
			<!-- content -->
			<div class="content" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; max-width: 600px; display: block; margin: 0 auto; padding: 0;">
				<table style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 0;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td align="center" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;">
<table style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; width: 100%; margin: 0; padding: 0;"><tr style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"><td class="padding" style="text-align: center; vertical-align: top; font-size: 0;">
									<a href="https://www.pyangelo.com/" style="text-decoration: none; font-weight: bold; text-align: center;"><img src="https://www.pyangelo.com/images/logos/pyangelo-logo.png" alt="PyAngelo - Learn to code" style="border-style: none" /></a>
								</td>
							</tr></table>
							<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.6; color: #666; font-weight: normal; margin: 0 0 10px; padding: 0;">$message
							</p>
						</td>
					</tr></table></div>
			<!-- /content -->
		</td>
		<td style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; margin: 0; padding: 0;"></td>
	</tr></table><!-- /footer --></body>
</html>
ENDEMAIL;
     return $footer;
  }
}
?>
