
/**
 *  rds securityグループ.
 */
module "wportal_rds_sg" {
  source      = "./module/security_group"
  name        = "wportal-rds-sg"
  vpc_id      = aws_vpc.aws_vpc_wportal.id
  port        = 3306
  cidr_blocks = [aws_vpc.aws_vpc_wportal.cidr_block]
  tag_name    = "wportal-rds-sg"
}

/**
 *  rdsサブネットグループ.
 */
#import {
#  to = aws_db_subnet_group.wportal_db_subnet_group
#  id = "wportal-db-subnet-group"
#}
resource "aws_db_subnet_group" "wportal_db_subnet_group" {
  name        = "wportal-db-subnet-group"
  description = "wportal-db-subnet-group"
  subnet_ids = [
    aws_subnet.private_0.id,
    aws_subnet.private_1.id
  ]
}

/**
 *  rdsクラスター
 */
resource "aws_rds_cluster" "wportal_rds_cluster" {
  cluster_identifier = "wportal-rds-cluster"

  db_subnet_group_name = aws_db_subnet_group.wportal_db_subnet_group.name
  vpc_security_group_ids = [
    module.wportal_rds_sg.security_group_id
  ]

  engine         = "aurora-mysql"
  engine_version = "8.0.mysql_aurora.3.05.2"
  port           = "3306"

  database_name   = "wportal"
  master_username = aws_ssm_parameter.wportal_db_username.value
  master_password = aws_ssm_parameter.wportal_db_password.value

  skip_final_snapshot = true

  db_cluster_parameter_group_name = aws_rds_cluster_parameter_group.wportal_rds_cluster_parameter_group.name
}

/**
 *  rdsクラスターインスタンス
 */
#import {
#  to = aws_rds_cluster_instance.wportal_rds_cluster_instance
#  id = "wportal-rds-cluster-instance"
#}
resource "aws_rds_cluster_instance" "wportal_rds_cluster_instance" {
  identifier         = "wportal-rds-cluster-instance"
  cluster_identifier = aws_rds_cluster.wportal_rds_cluster.id

  engine         = aws_rds_cluster.wportal_rds_cluster.engine
  engine_version = aws_rds_cluster.wportal_rds_cluster.engine_version

  instance_class       = "db.t4g.medium"
  db_subnet_group_name = aws_rds_cluster.wportal_rds_cluster.db_subnet_group_name
}

/**
 *  rdsクラスターコンフィグ
 */
#import {
#    to = aws_rds_cluster_parameter_group.wportal_rds_cluster_parameter_group
#    id = "wportal-rds-cluster-parameter-group"
#}
resource "aws_rds_cluster_parameter_group" "wportal_rds_cluster_parameter_group" {
  name   = "wportal-rds-cluster-parameter-group"
  family = "aurora-mysql8.0"

  parameter {
    name  = "time_zone"
    value = "Asia/Tokyo"
  }
}

