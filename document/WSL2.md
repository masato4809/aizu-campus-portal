# WSL2 + Ubuntuのセットアップ

## 各種機能の有効化

### Linux用Windowsサブシステムの有効化
1. PowerShellを管理者として開く 
2. PowerShellで以下のコマンドを実行する

```
dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
```

### 仮想マシンの有効化

1. PowerShellで以下のコマンドを実行する
2. PCを再起動する

```
dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
```

## Ubuntuの設定

1. PowerShellで以下のコマンドを実行する

```
wsl --install
```

2. PowerShellで以下の入力を行う

```
Create a default Unix user account:（任意のアカウント名）
New password:（任意のパスワード）
Retype new password:（任意のパスワードを再入力）
```

※入力したパスワードは表示されませんが正しく入力できています

3. 以下のメッセージが表示されることを確認する

```
We will save your answer to Windows and will only ask you once.
Would you like to opt-in to platform metrics collection (Y/n)? To see an example of the data collected, enter 'e'.
```

4. 以下の入力（選択）を行う

```
[Y/n/e]: n
```

## Ubuntuのメモリ上限の設定

1. `C:\Users\（ユーザ名）`に`.wslconfig`を作成する
2. 以下の値を入力して保存する

```
[wsl2]
memory=8GB
```

※こちらを設定しないとUbuntuがメモリの上限まで使います

## Node.jsのバージョンアップ

1. Ubuntuのアプリを開く
2. Ubuntuで以下のコマンドを順に実行する

### アップデートが可能なパッケージのリストを更新する

```
sudo apt update
```

### curlをインストールする

```
sudo apt install -y curl
```

### Node.jsをインストールする（v22の場合）

```
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo bash -
sudo apt-get install -y nodejs
```

### バージョンを確認する

```
node -v
# v22.23.0に近いバージョンが表示されたらOK

npm -v
# 10.9.8に近いバージョンが表示されたらOK
```

## その他の設定

### hostsファイルの編集
1. `C:\Windows\System32\drivers\etc`のフォルダを開く
2. `hosts`のファイルをメモ帳で管理者として開く
3. 以下の値を追記して保存する

```
127.0.0.1 localhost wportal2-minio
```

### gitのcrlfを無効化

以下のコマンドを実行する

```
git config --global core.autoCRLF false
```

## 最終確認

ここまで実施したら、PCを再起動してください。再起動後、以下の3点を確認してください。

1. **Ubuntuが起動するか**  
   Windows Keyを押して「ubuntu」と検索し、Ubuntuアプリが起動するか確認

2. **Ubuntuコマンドがターミナルで実行できるか**  
   PowerShellまたはコマンドプロンプトで `wsl` コマンドを実行してUbuntuコンソールが起動するか確認

3. **エクスプローラーでUbuntuファイルシステムにアクセスできるか**  
   エクスプローラーのアドレスバーに `\\wsl$` と入力して、Ubuntuのファイルシステムが見えるか確認

問題なければ、以上でWSL2とUbuntuのセットアップは完了です。