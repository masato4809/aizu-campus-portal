/**
 *  KMSによるパラメータ
 */

/**
 *  DBホスト.
 */
#import {
#  to = aws_ssm_parameter.wportal_db_host
#  id = "/wportal/db/host"
#}
resource "aws_ssm_parameter" "wportal_db_host" {
  name  = "/wportal/db/host"
  type  = "String"
  value = aws_rds_cluster.wportal_rds_cluster.endpoint

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  DBユーザー.
 */
#import {
#  to = aws_ssm_parameter.wportal_db_username
#  id = "/wportal/db/username"
#}
resource "aws_ssm_parameter" "wportal_db_username" {
  name        = "/wportal/db/username"
  type        = "String"
  value       = "wportal"
  description = "データベースの接続ユーザー"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  DBパスワード.
 */
#import {
#  to = aws_ssm_parameter.wportal_db_password
#  id = "/wportal/db/password"
#}
resource "aws_ssm_parameter" "wportal_db_password" {
  name        = "/wportal/db/password"
  type        = "SecureString"
  value       = "uninitialized"
  description = "データベースの接続パスワード"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  Redisホスト
 */
#import {
#  to = aws_ssm_parameter.wportal_redis_host
#  id = "/wportal/redis/host"
#}
resource "aws_ssm_parameter" "wportal_redis_host" {
  name  = "/wportal/redis/host"
  type  = "String"
  value = aws_elasticache_replication_group.wportal_elasticache_rg.primary_endpoint_address

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  APP_KEY
 */
import {
  to = aws_ssm_parameter.wportal_app_key
  id = "/wportal/app_key"
}
resource "aws_ssm_parameter" "wportal_app_key" {
  name  = "/wportal/app_key"
  type  = "SecureString"
  value = "uninitialized"
  description = "LARAVEL APP KEY"

    lifecycle {
        ignore_changes = [value]
    }
}

/**
 *  GOOGLE_CLIENT_ID
 */
#import {
#  to = aws_ssm_parameter.wportal_google_client_id
#  id = "/wportal/google_client_id"
#}
resource "aws_ssm_parameter" "wportal_google_client_id" {
  name        = "/wportal/google_client_id"
  type        = "SecureString"
  value       = "uninitialized"
  description = "GOOGLE_CLIENT_ID"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  GOOGLE_CLIENT_SECRET
 */
#import {
#  to = aws_ssm_parameter.wportal_google_client_secret
#  id = "/wportal/google_client_secret"
#}
resource "aws_ssm_parameter" "wportal_google_client_secret" {
  name        = "/wportal/google_client_secret"
  type        = "SecureString"
  value       = "uninitialized"
  description = "GOOGLE_CLIENT_SECRET"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  GOOGLE_CALENDAR_SA
 */
import {
 to = aws_ssm_parameter.wportal_google_calendar_sa
 id = "/wportal/google_calendar_sa"
}
resource "aws_ssm_parameter" "wportal_google_calendar_sa" {
  name        = "/wportal/google_calendar_sa"
  type        = "SecureString"
  value       = "uninitialized"
  description = "GOOGLE_CLIENT_SECRET"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  SLACK_APP_TOKEN
 */
#import {
#  to = aws_ssm_parameter.wportal_slack_app_token
#  id = "/wportal/slack_app_token"
#}
resource "aws_ssm_parameter" "wportal_slack_app_token" {
  name  = "/wportal/slack_app_token"
  type  = "SecureString"
  value = "uninitialized"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  SLACK_BOT_USER_OAUTH_TOKEN
 */
#import {
#  to = aws_ssm_parameter.wportal_slack_bot_user_oauth_token
#  id = "/wportal/slack_bot_user_oauth_token"
#}
resource "aws_ssm_parameter" "wportal_slack_bot_user_oauth_token" {
  name  = "/wportal/slack_bot_user_oauth_token"
  type  = "SecureString"
  value = "uninitialized"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  SLACK_URL_NOTICE
 */
#import {
#  to = aws_ssm_parameter.wportal_slack_url_notice
#  id = "/wportal/slack_url_notice"
#}
resource "aws_ssm_parameter" "wportal_slack_url_notice" {
  name  = "/wportal/slack_url_notice"
  type  = "SecureString"
  value = "uninitialized"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  RECAPTCHA_SECRET_KEY
 */
#import {
#  to = aws_ssm_parameter.wportal_recaptcha_secret_key
#  id = "/wportal/recaptcha_secret_key"
#}
resource "aws_ssm_parameter" "wportal_recaptcha_secret_key" {
  name  = "/wportal/recaptcha_secret_key"
  type  = "SecureString"
  value = "uninitialized"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  VITE_RECAPTCHA_SITE_KEY
 */
#import {
#  to = aws_ssm_parameter.wportal_vite_recaptcha_site_key
#  id = "/wportal/vite_recaptcha_site_key"
#}
resource "aws_ssm_parameter" "wportal_vite_recaptcha_site_key" {
  name  = "/wportal/vite_recaptcha_site_key"
  type  = "SecureString"
  value = "uninitialized"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  APP_AWS_ACCESS_KEY_ID
 */
#import {
#  to = aws_ssm_parameter.wportal_app_aws_access_key_id
#  id = "/wportal/app_aws_access_key_id"
#}
resource "aws_ssm_parameter" "wportal_app_aws_access_key_id" {
  name  = "/wportal/app_aws_access_key_id"
  type  = "SecureString"
  value = "uninitialized"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  APP_AWS_SECRET_ACCESS_KEY
 */
#import {
#  to = aws_ssm_parameter.wportal_app_aws_secret_access_key
#  id = "/wportal/app_aws_secret_access_key"
#}
resource "aws_ssm_parameter" "wportal_app_aws_secret_access_key" {
  name  = "/wportal/app_aws_secret_access_key"
  type  = "SecureString"
  value = "uninitialized"

  lifecycle {
    ignore_changes = [value]
  }
}

/**
 *  APP_AWS_REGION
 */
#import {
#  to = aws_ssm_parameter.wportal_app_aws_region
#  id = "/wportal/app_aws_region"
#}
resource "aws_ssm_parameter" "wportal_app_aws_region" {
  name  = "/wportal/app_aws_region"
  type  = "SecureString"
  value = "uninitialized"

  lifecycle {
    ignore_changes = [value]
  }
}
