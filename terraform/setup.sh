#! /bin/sh

# 引数が1つであるかチェック
if [ "$#" -ne 1 ]; then
  echo "引数が足りません。"
  echo "Usage: sh setup.sh <token-code>"
  exit 1
fi

echo "AWSへの接続を実行します... code: $1"

RES=`aws sts get-session-token --serial-number arn:aws:iam::979109089196:mfa/wportal --token-code $1`

AccessKeyId=$(echo "$RES" | jq -r '.Credentials.AccessKeyId')
SecretAccessKey=$(echo "$RES" | jq -r '.Credentials.SecretAccessKey')
SessionToken=$(echo "$RES" | jq -r '.Credentials.SessionToken')

echo $AccessKeyId

# 環境変数に設定
export AWS_ACCESS_KEY_ID=$AccessKeyId
export AWS_SECRET_ACCESS_KEY=$SecretAccessKey
export AWS_SESSION_TOKEN=$SessionToken

# tfvarsに出力.
echo "AWS_ACCESS_KEY = \"$AccessKeyId\"" > terraform.tfvars
echo "AWS_SECRET_KEY = \"$SecretAccessKey\"" >> terraform.tfvars
echo "AWS_SESSION_TOKEN = \"$SessionToken\"" >> terraform.tfvars

echo "AWSへの接続を実行しました."