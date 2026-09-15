# バリデーション（フロント/サーバー）について

どのようなページの作成であっても、入力値のバリデーションは存在し

大別すればフロント（TypeScript）で実施するバリデーションと、サーバー（PHP）で実施するバリデーションがあります

テンプレート環境ではいずれのケースについても、ある程度共通となる処理は用意しているため

利用方法、追加方法について記載します

## フロントのバリデーション

- 入力値がsubmitされる瞬間にTypeScript(JavaScript)側で実施する
- 判定結果の取得が超高速
- 通信を伴わないためUX的にも負荷的にもベスト
- サーバー側のデータ参照が必要なバリデーションは実施できない（もしくは、事前に準備が必要）

### フロントのバリデーションの流れ

基本的には送信直前に実施する

- 例）```script/Pages/InputForm/InputForm.tsx```
  - handleSaveGraphQLのコール時に実施
  - validateAll()で保持している入力値に対してバリデーションを実施する
  - validateAll()は該当データ専用の関数となるので、同ディレクトリに配置（```FormInputFormValidation.ts```）
    - 共通処理は```script/Common/Validation.ts```に記載
    - バリデーションを行いたいデータに対して、それぞれ下記のようなパラメータを設定する
      - 対象データ
        - 例）```inputSingleNumber```
      - 対象カラム名
        - 例）```input(single:整数)```
      - 実施バリデーション
        - 例）REQUIREDとNUMBERをErrorレベルとして
    - 上記の例では、フォームデータのinputSingleNumberに空でないこと・整数であることを確認する
  - バリデーションの結果は入力フォーム側で表示する可能性があるため、stateに保持しておく
  - エラーがある場合に、必要に応じて送信処理をキャンセルする

## サーバーのバリデーション

- GraphQLやPost通信で受け取った各パラメータをPHP側で実施する
- 基本的には必ず実施する必要がある（サーバー直叩き対策）
- 通信を伴い、結果取得に遅延がある

### サーバーのバリデーションの流れ

基本的にはサーバー側で値を受け取った直後に実施する

- 例）```app/GraphQL/App/Mutations/Pipe/PipeAppInputFormUpdate.php```
  - ```/app/GraphQL/App/Mutations/AppInputFormUpdate.php```で値を受け取った直後に実施
  - validate()で保持している入力値に対してバリデーションを実施する
  - validate()は値をparseするPipeクラスに配置
  - 共通処理は```app/Enum/App/EValidationType.php```に記載
  - バリデーションを行いたいデータに対して、それぞれ後述例のようにタグとのペアで記載する
    - タグ
    - カラム名
    - 値
    - ルールセット
  - ```$packer->makeValidator()```で実施

※２つの入力値にバリデーションをそれぞれ実施する場合の例）

```
        $packer = new ValidationPacker();
        $packer->addValidation(
            'input_single_number',
            'input(single:整数)',
            $this->inputSingleNumber,
            collect([
                EValidationType::REQUIRED->ruleSet(),
                EValidationType::NUMBER->ruleSet(),
            ])
        );
        $packer->addValidation(
            'input_single_not_zero',
            'input(single:非0)',
            $this->inputSingleNotZero,
            collect([
                EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
            ])
        );
```

実施したバリデーション結果はJson形式となり、そのままフロントで表示する事ができない

```script/Pages/InputForm/FormInputFormValidation.ts```などのファイルで

```parseServerValidation```関数を作成し、サーバーで作成したバリデーションタグと値の紐づけを行うことで利用できる

## バリデーション結果の扱い

バリデーション結果はstateとして入力フォームに保持しておく

```<InputField />```コンポーネントに

```errors={validation.data.inputSingleNumber?.errors}```と記載することで

inputSingleNumberのエラー情報が存在する場合にのみ、エラー結果が表示されるようになる

表示そのもののOn/Offは```visibleError```フラグで制御する

## 推奨するバリデーション設定

サーバー側のバリデーションは事実上必須なため （APIが外部に絶対に公開されていない場合のみ除外可能）

準備優先度としては```サーバー > フロント```として、フロントの用意有無はQCDと相談

ただし基本的には両バリデーションを揃えておいたほうが良い

この場合、サーバーとフロントのバリデーションが完全に一致している必要があり

そのためにも前述のEnum定義の仕組みで動作を揃えておくことを推奨する

## 用意したバリデーション種別

ひとまず下記を作成、

テンプレートの利用開始後は必要に応じて自由に追加/修正してください

rangeやmaxなどは外部から数値を渡せるようになっています

| Enum              | 内容            | Laravel処理                                       |
|-------------------|---------------|-------------------------------------------------|
| REQUIRED          | 空白を許可しない      | ```['required']```                              |
| REQUIRED_NOT_ZERO | 空白・0を許可しない    | ```['required', 'not_in:0']```                  |
| NUMBER            | 数値のみ          | ```['numeric']```                               |
| NUMBER_HYPHEN     | 数値とハイフンのみ     | ```['regex:/^[0-9-]+$/']```                     |
| NUMBER_PLUS       | 0以上の数値のみ      | ```['numeric', 'gte:0']```                      |
| NUMBER_MAX        | 数値の最大値        | ```['numeric', "max:$value1"]```                |
| NUMBER_RANGE      | min〜maxの範囲内のみ | ```['numeric', "gte:$value1", "lte:$value2"]``` |
| MAX_DIGIT         | max桁以下の整数のみ   | ```["max_digits:$value1"]```                    |
| LENGTH            | 文字数がmax以下     | ```[new RuleMaxLength($value1)]```              |
| PHONE             | 電話番号          | ```['regex:/^[0-9-]{10,13}$/']```               |
| EMAIL             | Eメール          | regexが長いため省略                                    |
