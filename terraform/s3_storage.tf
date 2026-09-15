/**
 *  wportalストレージバケット.
 */
#import {
#  to = aws_s3_bucket.wportal_storage
#  id = "wportal-storage"
#}
resource "aws_s3_bucket" "wportal_storage" {
  bucket = "wportal-storage"
}

/**
 *  ブロックパブリックアクセス.
 */
#import {
#  to = aws_s3_bucket_public_access_block.wportal_storage
#  id = "wportal-storage"
#}
resource "aws_s3_bucket_public_access_block" "wportal_storage" {
  bucket                  = aws_s3_bucket.wportal_storage.id
  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

/**
 *  暗号化設定.
 */
#import {
#  to = aws_s3_bucket_server_side_encryption_configuration.wportal_storage
#  id = "wportal-storage"
#}
resource "aws_s3_bucket_server_side_encryption_configuration" "wportal_storage" {
  bucket = aws_s3_bucket.wportal_storage.id
  rule {
    apply_server_side_encryption_by_default {
      sse_algorithm = "AES256"
    }
  }
}