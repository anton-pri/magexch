<font size="2">
{assign var="link" value="<a href=\"`$catalogs.customer`\" target=\"_new\">`$config.Company.company_name`</a>"}
{$lng.eml_mail_header|substitute:"company":$link}
</font>
