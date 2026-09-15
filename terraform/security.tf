/**
 * セキュリティグループ
 */

/**
 *  HTTPロードバランサ
 */
module "http_sg" {
  source      = "./module/security_group"
  name        = "wportal-http-sg"
  vpc_id      = aws_vpc.aws_vpc_wportal.id
  port        = 80
  cidr_blocks = ["0.0.0.0/0"]

  tag_name = "wportal-http-sg"
}

/**
 *  HTTPSロードバランサ
 */
module "https_sg" {
  source      = "./module/security_group"
  name        = "wportal-https-sg"
  vpc_id      = aws_vpc.aws_vpc_wportal.id
  port        = 443
  cidr_blocks = ["0.0.0.0/0"]

  tag_name = "wportal-https-sg"
}

/**
 *  リダイレクト用
 */
module "http_redirect_sg" {
  source      = "./module/security_group"
  name        = "wportal-http-redirect-sg"
  vpc_id      = aws_vpc.aws_vpc_wportal.id
  port        = 8080
  cidr_blocks = ["0.0.0.0/0"]

  tag_name = "wportal-http-redirect-sg"
}