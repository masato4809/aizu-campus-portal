# aws cli / terraform の接続方法

- 作成したIAMがengineerグループの場合、MFAセッショントークンがないと接続不可
- 最初に一時接続情報を取得して、環境変数/設定ファイルに入力しておく

## 一時トークンの取得

- aws cliで下記を実行
```
aws sts get-session-token --serial-number iam-role-name --token-code XXXXXX
```
iam-role-nameは親iamではなく、MFA認証に設定されたmfa/user-nameを指定することに注意

- 下記の情報を取得する

```
{
    "Credentials": {
        "SecretAccessKey": "secret-access-key",
        "SessionToken": "temporary-session-token",
        "Expiration": "expiration-date-time",
        "AccessKeyId": "access-key-id"
    }
}
```

## aws cli への適用

- 下記を実行する

```
export AWS_ACCESS_KEY_ID=example-access-key-as-in-previous-output
export AWS_SECRET_ACCESS_KEY=example-secret-access-key-as-in-previous-output
export AWS_SESSION_TOKEN=example-session-token-as-in-previous-output
```

- 下記のコマンドなどで認証を確認する

```
aws iam list-users
```

## terraform への適用

- terraform.tfvars（.gitignore対象）の値を更新する

```
AWS_ACCESS_KEY = "example-access-key-as-in-previous-output"
AWS_SECRET_KEY = "example-secret-access-key-as-in-previous-output"
AWS_SESSION_TOKEN = "example-session-token-as-in-previous-output"
```

- variables.tfで上記値がロードされ、terraformのapplyが実行可能になる
