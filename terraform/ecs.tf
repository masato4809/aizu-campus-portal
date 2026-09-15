/**
 *  ECRリポジトリ
 *  サービスコンテナを保存.
 */
resource "aws_ecr_repository" "wportal2_ecr_service_container" {
  name = "wportal2_ecr_service_container"
}

data "external" "ecr_image_wportal2_newest" {
  program = [
    "aws", "ecr", "describe-images",
    "--repository-name", "wportal2_ecr_service_container",
    "--query", "{\"tags\": to_string(sort_by(imageDetails,& imagePushedAt)[-1].imageTags)}",
    "--region", "ap-northeast-1"
  ]
}

locals {
  ecr_wportal2_repository_newest_tags = jsondecode(data.external.ecr_image_wportal2_newest.result.tags)
}

data "aws_ecr_repository" "service_container" {
  name = "wportal2_ecr_service_container"
}

/**
 *  ECSクラスタ
 */
resource "aws_ecs_cluster" "wportal2_ecs_cluster" {
  name = "wportal2-ecs-cluster"

  setting {
    name  = "containerInsights"
    value = "enabled"
  }

  tags = {
    Name = "wportal2-ecs-cluster"
  }
}

resource "aws_ecs_cluster_capacity_providers" "wportal2_ecs_capacity_providers" {
  cluster_name = aws_ecs_cluster.wportal2_ecs_cluster.name

  capacity_providers = ["FARGATE"]

  default_capacity_provider_strategy {
    base              = 1
    weight            = 100
    capacity_provider = "FARGATE"
  }
}

resource "aws_iam_role" "ecs_task_execution_role" {
  name = "wportal2_ecs_task_execution_role"
  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect    = "Allow"
        Principal = { Service = "ecs-tasks.amazonaws.com" }
        Action    = "sts:AssumeRole"
      }
    ]
  })
  managed_policy_arns = [
    "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy",
    "arn:aws:iam::aws:policy/AmazonSSMReadOnlyAccess"
  ]
}

resource "aws_iam_role_policy" "ecs_task_execution_role_policy" {
  name = "wportal2_ecs_task_execution_role_policy_kms"
  role = aws_iam_role.ecs_task_execution_role.id
  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        "Effect" : "Allow",
        "Action" : [
          "ssm:GetParameters",
          "kms:Decrypt",
          "s3:PutObject",
          "s3:GetObject",
          "s3:DeleteObject",
          "s3:GetObjectVersion",
          "s3:GetBucketVersioning",
          "ssmmessages:CreateControlChannel",
          "ssmmessages:CreateDataChannel",
          "ssmmessages:OpenControlChannel",
          "ssmmessages:OpenDataChannel",
        ],
        "Resource" : ["*"]
      }
    ]
  })
}

#/**
# *  タスク定義
# */
resource "aws_ecs_task_definition" "wportal2_ecs_task" {
  family                   = "wportal2-task"
  cpu                      = "256"
  memory                   = "512"
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  execution_role_arn       = aws_iam_role.ecs_task_execution_role.arn
  task_role_arn            = aws_iam_role.ecs_task_execution_role.arn

  container_definitions = jsonencode([
    {
      name      = "wportal2-container"
      essential = true
      image     = "${data.aws_ecr_repository.service_container.repository_url}:${local.ecr_wportal2_repository_newest_tags[0]}"
      logConfiguration = {
        logDriver = "awslogs"
        options = {
          awslogs-region        = "ap-northeast-1"
          awslogs-stream-prefix = "httpd"
          awslogs-group         = "/wportal2/ecs"
        }
      }
      portMappings = [
        {
          containerPort = 80
          protocol      = "tcp"
        }
      ]
      command = [
        "/usr/local/bin/server.sh"
      ]
      secrets = [
        {
          name      = "DB_HOST"
          valueFrom = "/wportal/db/host"
        },
        {
          name      = "DB_USERNAME"
          valueFrom = "/wportal/db/username"
        },
        {
          name      = "DB_PASSWORD"
          valueFrom = "/wportal/db/password"
        },
        {
          name      = "REDIS_HOST"
          valueFrom = "/wportal/redis/host"
        },
        {
          name      = "GOOGLE_CLIENT_ID"
          valueFrom = "/wportal/google_client_id"
        },
        {
          name      = "GOOGLE_CLIENT_SECRET"
          valueFrom = "/wportal/google_client_secret"
        },
        {
          name      = "GOOGLE_CALENDAR_SA"
          valueFrom = "/wportal/google_calendar_sa"
        },
        {
          name      = "SLACK_APP_TOKEN"
          valueFrom = "/wportal/slack_app_token"
        },
        {
          name      = "SLACK_BOT_USER_OAUTH_TOKEN"
          valueFrom = "/wportal/slack_bot_user_oauth_token"
        },
        {
          name      = "SLACK_URL_NOTICE"
          valueFrom = "/wportal/slack_url_notice"
        },
        {
          name      = "RECAPTCHA_SECRET_KEY"
          valueFrom = "/wportal/recaptcha_secret_key"
        },
        {
          name      = "VITE_RECAPTCHA_SITE_KEY"
          valueFrom = "/wportal/vite_recaptcha_site_key"
        },
        {
          name      = "APP_AWS_ACCESS_KEY_ID"
          valueFrom = "/wportal/app_aws_access_key_id"
        },
        {
          name      = "APP_AWS_SECRET_ACCESS_KEY"
          valueFrom = "/wportal/app_aws_secret_access_key"
        },
        {
          name      = "APP_AWS_REGION"
          valueFrom = "/wportal/app_aws_region"
        }
      ],
      environment = [
        {
          name  = "DB_DATABASE"
          value = "wportal"
        },
      ]
    }
  ])

  tags = {
    Name = "wportal2-ecs-task"
  }
}

/**
 *  ECSサービス
 */
resource "aws_ecs_service" "wportal2_ecs_service" {
  name                               = "wportal2-ecs-service"
  cluster                            = aws_ecs_cluster.wportal2_ecs_cluster.arn
  task_definition                    = aws_ecs_task_definition.wportal2_ecs_task.arn
  desired_count                      = 1
  launch_type                        = "FARGATE"
  enable_execute_command             = true
  platform_version                   = "1.4.0"
  health_check_grace_period_seconds  = 1800
  deployment_minimum_healthy_percent = 50
  deployment_maximum_percent         = 200

  network_configuration {
    assign_public_ip = false
    security_groups  = [module.apache_sg.security_group_id]

    subnets = [
      aws_subnet.private_0.id,
      aws_subnet.private_1.id,
    ]
  }

  load_balancer {
    target_group_arn = aws_alb_target_group.alb_target_group.arn
    container_name   = "wportal2-container"
    container_port   = 80
  }

  lifecycle {
    ignore_changes = [task_definition]
  }
}

module "apache_sg" {
  source      = "./module/security_group"
  name        = "apache-sg"
  vpc_id      = aws_vpc.aws_vpc_wportal.id
  port        = 80
  cidr_blocks = [aws_vpc.aws_vpc_wportal.cidr_block]
  tag_name    = "wportal-apache-sg"
}