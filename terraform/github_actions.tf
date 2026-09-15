/**
 *  GitHubActionsから接続するための情報.
 */

/**
 * IDプロバイダ.
 */
resource "aws_iam_openid_connect_provider" "wportal2_github_actions" {
  url = "https://token.actions.githubusercontent.com"
  client_id_list  = ["sts.amazonaws.com"]
  thumbprint_list = [
    "1c58a3a8518e8759bf075b76b750d4f2df264fcd",
    "f879abce0008e4eb126e0097e46620f5aaae26ad"
  ]
}

/**
 * 接続用ロール
 */
data "aws_iam_policy_document" "wportal2_deploy_assume_role_policy" {

  // for GitHubActions.
  statement {
    effect  = "Allow"
    actions = [
      "sts:AssumeRoleWithWebIdentity",
    ]
    principals {
      type        = "Federated"
      identifiers = ["arn:aws:iam::979109089196:oidc-provider/token.actions.githubusercontent.com"] # ID プロバイダの ARN
    }
    condition {
      test     = "StringEquals"
      variable = "token.actions.githubusercontent.com:aud"
      values   = ["sts.amazonaws.com"]
    }

    # 特定のリポジトリの特定のブランチからのみ認証を許可する
    condition {
      test     = "StringLike"
      variable = "token.actions.githubusercontent.com:sub"
      values   = ["repo:quick-wpd-human-tech/wportal2:*"]
    }
  }
}

resource "aws_iam_role" "wportal2_deploy_iam_role" {
  name = "wportal2_deploy_iam_role"
  assume_role_policy = data.aws_iam_policy_document.wportal2_deploy_assume_role_policy.json
}

# 任意のポリシーをアタッチする
resource "aws_iam_role_policy_attachment" "wportal2_deploy_policy_attachment" {
  role       = aws_iam_role.wportal2_deploy_iam_role.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonS3ReadOnlyAccess"
}

# ECRログイン/タスク更新用.
data "aws_iam_policy_document" "wportal2_ecr_policy_document" {
    statement {
      sid = "AllowPush"
      effect = "Allow"
      actions = [
        "ecr:GetAuthorizationToken",
        "ecr:PutImage",
        "ecr:InitiateLayerUpload",
        "ecr:UploadLayerPart",
        "ecr:CompleteLayerUpload",
        "ecr:BatchCheckLayerAvailability",
        "ecs:DescribeServices",
        "ecs:DescribeTaskDefinition",
        "ecs:DescribeTasks",
        "ecs:DescribeClusters",
        "ecs:ListTasks",
        "ecs:ListTaskDefinitions",
        "ecs:RegisterTaskDefinition",
        "ecs:UpdateService",
        "ecs:TagResource",
        "ecs:RunTask",
        "ecs:StopTask",
        "ecs:ExecuteCommand",
        "ec2:DescribeSubnets",
        "ec2:DescribeVpcEndpoints",
        "iam:PassRole",
        "iam:ListRoles",
        "iam:SimulatePrincipalPolicy",
        "ssm:GetParameter",
        "ssm:GetParameters",
        "logs:*",
      ]
      resources = ["*"]
    }
}

resource "aws_iam_policy" "wportal2_ecr_policy" {
  name   = "wportal2_deploy_policy"
  policy = data.aws_iam_policy_document.wportal2_ecr_policy_document.json
}

resource "aws_iam_role_policy_attachment" "wportal2_ecr_policy_attachment" {
  role       = aws_iam_role.wportal2_deploy_iam_role.name
  policy_arn = aws_iam_policy.wportal2_ecr_policy.arn
}