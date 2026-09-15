/**
 *  ElastiCacheパラメータグループ.
 */
#import {
#  to = aws_elasticache_parameter_group.wportal_elasticache_pg
#  id = "wportal-elasticache-pg"
#}
resource "aws_elasticache_parameter_group" "wportal_elasticache_pg" {
  family = "redis6.x"
  name   = "wportal-elasticache-pg"

  parameter {
    name  = "cluster-enabled"
    value = "no"
  }
}

/**
 *  ElastiCacheサブネットグループ.
 */
#import {
#  to = aws_elasticache_subnet_group.wportal_elasticache_sg
#  id = "wportal-elasticache-sg"
#}
resource "aws_elasticache_subnet_group" "wportal_elasticache_sg" {
  name = "wportal-elasticache-sg"
  subnet_ids = [
    aws_subnet.private_0.id,
    aws_subnet.private_1.id
  ]
}

/**
 *  ElastiCacheレプリケーショングループ.
 */
resource "aws_elasticache_replication_group" "wportal_elasticache_rg" {
  description          = "wportal (Cluster Disabled)"
  replication_group_id = "wportal-elasticache-rg"
  #replication_group_description = "wportal (Cluster Disabled)"
  engine                     = "redis"
  engine_version             = "6.2"
  num_cache_clusters         = 2
  node_type                  = "cache.t3.micro"
  snapshot_window            = "09:10-10:10"
  snapshot_retention_limit   = 7
  maintenance_window         = "mon:10:40-mon:11:40"
  automatic_failover_enabled = true
  port                       = 6379
  apply_immediately          = false
  security_group_ids = [
    module.redis_sg.security_group_id
  ]
  parameter_group_name = aws_elasticache_parameter_group.wportal_elasticache_pg.name
  subnet_group_name    = aws_elasticache_subnet_group.wportal_elasticache_sg.name
}

module "redis_sg" {
  source      = "./module/security_group"
  name        = "redis-sg"
  vpc_id      = aws_vpc.aws_vpc_wportal.id
  port        = 6379
  cidr_blocks = [aws_vpc.aws_vpc_wportal.cidr_block]
  tag_name    = "wportal-redis-sg"
}