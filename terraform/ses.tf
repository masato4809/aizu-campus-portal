/**
 *  Simple Email Service.
 */

/**
 *  domain-identify
 */
#import {
#  to = aws_ses_domain_identity.wportal_ses_domain_identify
#  id = "wportal.org"
#}
resource "aws_ses_domain_identity" "wportal_ses_domain_identify" {
  domain = data.aws_route53_zone.wportal.name
}

#import {
#    to = aws_route53_record.wportal_ses_txt
#    id = "Z010452446PHWCZNVO30__amazonses.wportal.org_TXT"
#}
resource "aws_route53_record" "wportal_ses_txt" {
  zone_id = data.aws_route53_zone.wportal.zone_id
  name    = "_amazonses.${data.aws_route53_zone.wportal.name}"
  type    = "TXT"
  ttl     = 600
  records = [
    aws_ses_domain_identity.wportal_ses_domain_identify.verification_token
  ]
}

/**
 *  DKIM
 */
#import {
#  to = aws_ses_domain_dkim.wportal_ses_domain_dkim
#  id = "wportal.org"
#}
resource "aws_ses_domain_dkim" "wportal_ses_domain_dkim" {
  domain = data.aws_route53_zone.wportal.name
}

#import {
#  to = aws_route53_record.wportal_ses_cname_dkim_1
#  id = "Z010452446PHWCZNVO30_4reg35qsq2tjeth4rn5cq77vyp37aucz._domainkey.wportal.org_CNAME"
#}
resource "aws_route53_record" "wportal_ses_cname_dkim_1" {
  zone_id = data.aws_route53_zone.wportal.zone_id
  name    = "4reg35qsq2tjeth4rn5cq77vyp37aucz._domainkey.wportal.org"
  type    = "CNAME"
  ttl     = 600
  records = [
    "4reg35qsq2tjeth4rn5cq77vyp37aucz.dkim.amazonses.com"
  ]
}

#import {
#    to = aws_route53_record.wportal_ses_cname_dkim_2
#    id = "Z010452446PHWCZNVO30_d2yad5hqxfrnnkcwikbtkzgi2oonjnvw._domainkey.wportal.org_CNAME"
#}
resource "aws_route53_record" "wportal_ses_cname_dkim_2" {
  zone_id = data.aws_route53_zone.wportal.zone_id
  name    = "d2yad5hqxfrnnkcwikbtkzgi2oonjnvw._domainkey.wportal.org"
  type    = "CNAME"
  ttl     = 600
  records = [
    "d2yad5hqxfrnnkcwikbtkzgi2oonjnvw.dkim.amazonses.com"
  ]
}

#import {
#  to = aws_route53_record.wportal_ses_cname_dkim_3
#  id = "Z010452446PHWCZNVO30_prfcm5yt23ba4crh73y2mcanl3k6ezyq._domainkey.wportal.org_CNAME"
#}
resource "aws_route53_record" "wportal_ses_cname_dkim_3" {
  zone_id = data.aws_route53_zone.wportal.zone_id
  name    = "prfcm5yt23ba4crh73y2mcanl3k6ezyq._domainkey.wportal.org"
  type    = "CNAME"
  ttl     = 600
  records = [
    "prfcm5yt23ba4crh73y2mcanl3k6ezyq.dkim.amazonses.com"
  ]
}

/**
 *  SPF
 */
#import {
#  to = aws_ses_domain_mail_from.wportal_ses_domain_mail_from
#  id = "wportal.org"
#}
resource "aws_ses_domain_mail_from" "wportal_ses_domain_mail_from" {
  domain           = data.aws_route53_zone.wportal.name
  mail_from_domain = "mail.${data.aws_route53_zone.wportal.name}"
}

#import {
#  to = aws_route53_record.wportal_ses_mx_mail
#  id = "Z010452446PHWCZNVO30_mail.wportal.org_MX"
#}
resource "aws_route53_record" "wportal_ses_mx_mail" {
  zone_id = data.aws_route53_zone.wportal.zone_id
  name    = aws_ses_domain_mail_from.wportal_ses_domain_mail_from.mail_from_domain
  type    = "MX"
  ttl     = 600
  records = [
    "10 feedback-smtp.ap-northeast-1.amazonses.com"
  ]
}

#import {
#  to = aws_route53_record.wportal_ses_txt_mail
#  id = "Z010452446PHWCZNVO30_mail.wportal.org_TXT"
#}
resource "aws_route53_record" "wportal_ses_txt_mail" {
  zone_id = data.aws_route53_zone.wportal.zone_id
  name    = aws_ses_domain_mail_from.wportal_ses_domain_mail_from.mail_from_domain
  type    = "TXT"
  ttl     = 600
  records = [
    "v=spf1 include:amazonses.com ~all"
  ]
}

/**
 *  DMARC
 */
#import {
#    to = aws_route53_record.wportal_ses_txt_dmarc
#    id = "Z010452446PHWCZNVO30__dmarc.wportal.org_TXT"
#}
resource "aws_route53_record" "wportal_ses_txt_dmarc" {
  zone_id = data.aws_route53_zone.wportal.zone_id
  name    = "_dmarc.wportal.org"
  type    = "TXT"
  ttl     = 600
  records = [
    "v=DMARC1;p=quarantine;pct=25;rua=mailto:dmarcreports@wportal.org"
  ]
}
