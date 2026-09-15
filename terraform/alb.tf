/**
 *  アプリケーションロードバランサ
 */
#import {
#  to = aws_alb.wportal_alb
#  id = "arn:aws:elasticloadbalancing:ap-northeast-1:979109089196:loadbalancer/app/wportal-alb/a71bca8434e305e6"
#}
resource "aws_alb" "wportal_alb" {
  name                       = "wportal-alb"
  load_balancer_type         = "application"
  internal                   = false
  idle_timeout               = 60
  enable_deletion_protection = false # とりあえず削除保護は無効

  subnets = [
    aws_subnet.public_0.id,
    aws_subnet.public_1.id
  ]

  access_logs {
    bucket  = aws_s3_bucket.alb_log.id
    enabled = true
  }

  security_groups = [
    module.http_sg.security_group_id,
    module.https_sg.security_group_id,
    module.http_redirect_sg.security_group_id
  ]

  tags = {
    Name = "wportal-alb"
  }
}

output "alb_dns_name" {
  value = aws_alb.wportal_alb.dns_name
}

/**
 *  HTTPリスナー
 */
#import {
#  to = aws_alb_listener.http_listener
#  id = "arn:aws:elasticloadbalancing:ap-northeast-1:979109089196:listener/app/wportal-alb/a71bca8434e305e6/da3ebc2011c1d9bc"
#}
resource "aws_alb_listener" "http_listener" {
  load_balancer_arn = aws_alb.wportal_alb.arn
  port              = "80"
  protocol          = "HTTP"

  default_action {
    type = "fixed-response"

    fixed_response {
      content_type = "text/plain"
      message_body = "this is http."
      status_code  = "200"
    }
  }
}

/**
 *  ホストゾーンのデータソース
 */
data "aws_route53_zone" "wportal" {
  name = "wportal.org"
}

/**
 *  ホストゾーン
 */
#import {
#    to = aws_route53_zone.app_wportal
#    id = "Z0073710LR6OQP9ZR0NX"
#}
resource "aws_route53_zone" "app_wportal" {
  name = "app.wportal.com"

  tags = {
    Name = "wportal-host-zone"
  }
}

/**
 *  DNSレコード
 */
#import {
#  to = aws_route53_record.app
#  id = "Z010452446PHWCZNVO30_wportal.org_A"
#}
resource "aws_route53_record" "app" {
  name    = data.aws_route53_zone.wportal.name
  type    = "A"
  zone_id = data.aws_route53_zone.wportal.zone_id

  alias {
    evaluate_target_health = true
    name                   = aws_alb.wportal_alb.dns_name
    zone_id                = aws_alb.wportal_alb.zone_id
  }
}

/**
 *  SSL証明書定義
 */
#import {
#  to = aws_acm_certificate.app_certificate
#  id = "arn:aws:acm:ap-northeast-1:979109089196:certificate/fd338d1f-d006-4ab4-b84a-c770c77c28b2"
#}
resource "aws_acm_certificate" "app_certificate" {
  domain_name               = data.aws_route53_zone.wportal.name
  subject_alternative_names = []
  validation_method         = "DNS"

  lifecycle {
    create_before_destroy = true
  }
}

/**
 *  SSL証明書の検証用レコード.
 */
resource "aws_route53_record" "app_certificate_record" {
  for_each = {
    for dvo in aws_acm_certificate.app_certificate.domain_validation_options : dvo.domain_name => {
      name   = dvo.resource_record_name
      type   = dvo.resource_record_type
      record = dvo.resource_record_value
    }
  }

  allow_overwrite = true
  name            = each.value.name
  type            = each.value.type
  records         = [each.value.record]
  zone_id         = data.aws_route53_zone.wportal.id
  ttl             = 60
}

/**
 *  apply時にSSL検証の完了を待つ
 */
resource "aws_acm_certificate_validation" "app_certificate_validation" {
  certificate_arn = aws_acm_certificate.app_certificate.arn
  validation_record_fqdns = [
    for record in aws_route53_record.app_certificate_record : record.fqdn
  ]
}

/**
 *  HTTPSリスナー
 */
#import {
#  to = aws_alb_listener.https
#  id = "arn:aws:elasticloadbalancing:ap-northeast-1:979109089196:listener/app/wportal-alb/a71bca8434e305e6/8f6ff6399b1f01c1"
#}
resource "aws_alb_listener" "https" {
  load_balancer_arn = aws_alb.wportal_alb.arn
  port              = "443"
  protocol          = "HTTPS"
  certificate_arn   = aws_acm_certificate.app_certificate.arn
  ssl_policy        = "ELBSecurityPolicy-2016-08"
  default_action {
    type = "fixed-response"

    fixed_response {
      content_type = "text/plain"
      message_body = "this is https."
      status_code  = "200"
    }
  }

  depends_on = [
    aws_acm_certificate_validation.app_certificate_validation
  ]
}

/**
* HTTP->HTTPSリダイレクトリスナー
*/
#import {
#  to = aws_alb_listener.redirect_http_to_https
#  id = "arn:aws:elasticloadbalancing:ap-northeast-1:979109089196:listener/app/wportal-alb/a71bca8434e305e6/9a67c053353676a4"
#}
resource "aws_alb_listener" "redirect_http_to_https" {
  load_balancer_arn = aws_alb.wportal_alb.arn
  port              = "8080"
  protocol          = "HTTP"
  default_action {
    type = "redirect"

    redirect {
      port        = "443"
      protocol    = "HTTPS"
      status_code = "HTTP_301"
    }
  }
}

/**
 *  転送先のターゲットグループ.
 */
#import {
#  to = aws_alb_target_group.alb_target_group
#  id = "arn:aws:elasticloadbalancing:ap-northeast-1:979109089196:targetgroup/alb-target-group/bb3f69b01a6c5a88"
#}
resource "aws_alb_target_group" "alb_target_group" {
  name                 = "alb-target-group"
  vpc_id               = aws_vpc.aws_vpc_wportal.id
  target_type          = "ip"
  port                 = 80
  protocol             = "HTTP"
  deregistration_delay = 5
  health_check {
    path                = "/api/healthcheck"
    healthy_threshold   = 2
    unhealthy_threshold = 2
    timeout             = 4
    interval            = 5
  }

  depends_on = [aws_alb.wportal_alb]
}

/**
 *  リスナールール.
 */
#import {
#  to = aws_alb_listener_rule.alb_listener_rule
#  id = "arn:aws:elasticloadbalancing:ap-northeast-1:979109089196:listener-rule/app/wportal-alb/a71bca8434e305e6/8f6ff6399b1f01c1/af6cfda5e1597d65"
#}
resource "aws_alb_listener_rule" "alb_listener_rule" {
  listener_arn = aws_alb_listener.https.arn
  priority     = 100

  action {
    type             = "forward"
    target_group_arn = aws_alb_target_group.alb_target_group.arn
  }

  condition {
    path_pattern {
      values = ["/*"]
    }
  }
}