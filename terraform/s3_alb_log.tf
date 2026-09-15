/**
 *  ALBログバケット
 */
#import {
#  to = aws_s3_bucket.alb_log
#    id = "wportal-alb-log"
#}
resource "aws_s3_bucket" "alb_log" {
  bucket = "wportal-alb-log"
}

/**
 *  ブロックパブリックアクセス.
 */
#import {
#    to = aws_s3_bucket_public_access_block.alb_log
#    id = "wportal-alb-log"
#}
resource "aws_s3_bucket_public_access_block" "alb_log" {
  bucket                  = aws_s3_bucket.alb_log.id
  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

/**
 *  暗号化設定.
 */
#import {
#    to = aws_s3_bucket_server_side_encryption_configuration.alb_log
#    id = "wportal-alb-log"
#}
resource "aws_s3_bucket_server_side_encryption_configuration" "alb_log" {
  bucket = aws_s3_bucket.alb_log.id
  rule {
    apply_server_side_encryption_by_default {
      sse_algorithm = "AES256"
    }
  }
}

/**
 *  ライフサイクル設定.
 */
import {
   to = aws_s3_bucket_lifecycle_configuration.alb_log
   id = "wportal-alb-log"
}
resource "aws_s3_bucket_lifecycle_configuration" "alb_log" {
  bucket = aws_s3_bucket.alb_log.id
  rule {
    id     = "wportal-alb-log-lifecycle"
    status = "Enabled"
    expiration {
      days = "30"
    }
  }
}

/**
 *  パケットポリシー
 */
#import {
#  to = aws_s3_bucket_policy.alb_log
#  id = "wportal-alb-log"
#}
resource "aws_s3_bucket_policy" "alb_log" {
  bucket = aws_s3_bucket.alb_log.id
  policy = data.aws_iam_policy_document.alb_log.json
}

/**
 *  ポリシードキュメント
 */
data "aws_iam_policy_document" "alb_log" {
  statement {
    effect    = "Allow"
    actions   = ["s3:PutObject"]
    resources = ["arn:aws:s3:::${aws_s3_bucket.alb_log.id}/*"]

    principals {
      identifiers = ["582318560864"]
      type        = "AWS"
    }
  }
}