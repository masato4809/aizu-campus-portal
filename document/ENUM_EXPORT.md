# 推奨するEnumの利用・自動出力について

## PHPの列挙型(Enum)の利用について

PHP8.1より列挙型(Enum)が標準で利用できるようになっています。

例）何かの種別を```ESampleType```として表現するなら例えば下記のようになります。

```
enum ESampleType: int
{
  case INVALID = 0;  // 無効を表す
  case DOCUMENT = 1; // 種別・ドキュメントを表す
  case BINARY = 2;   // 種別・バイナリを表す
  case IMAGE = 3;    // 種別・画像を表す
  
  /**
   * 指定したEnumに対してメソッドが設定できます
   */
  public functin isBinary(): bool
  {
    return match($this) {
      self::BINARY,
      self::IMAGE => true,
      default => false    
    }
  }
}
```

Enumの利点は基本的にマジックナンバーの除去です。

下記のようなメリットがあり積極的に利用してください。

### 処理が見やすくなる

```
if ($type === 2) {
  // BINARYの場合の処理.
  return;
}
```

という記述が

```
if ($type === ESampleType::BINARY) {
  // BINARYの場合の処理.
  return;
}
```

となり、実装者もレビュアーも何を意図しているのかわかりやすくなります。

### 運用上、コードの該当箇所を探しやすい

上記の例で、ESampleType::BINARYの判定箇所の仕様が変更になった場合

```ESampleType::BINARY```で検索すれば、基本的には抜け落ちることはないです

※ ```$type === 2```と記載されていると該当箇所かそうでないのかを判断するのはやや困難です

### 値の変更を行っても問題ない場合が多い

あまり推奨するやり方ではないですが、下記のような変更を加えたとしてもコードに影響はありません

※とはいえど、完全に影響のない追加の方が良いと思います

```
  case DOCUMENT = 1; // 種別・ドキュメントを表す
  case SOUND = 2;    // 種別・音声（2として追加し、従来のEnumをスライド）
  case BINARY = 3;   // 種別・バイナリを表す
  case IMAGE = 4;    // 種別・画像を表す
```

## TypeScriptの列挙型の利用について

Enumの利用有無についてはPHPと同様積極的に利用すべきです。

メリットのみを考慮すれば標準のEnumを利用することでも問題ありませんが、一部非推奨とされている要素があり

あまり強い動機ではないもののUnion型を標準としています。

記載の例としては下記のようになります。

```
export const E_SAMPLE_TYPE = {
  INVALID : 0,  // 無効を表す
  DOCUMENT : 1, // 種別・ドキュメントを表す
  BINARY : 2,   // 種別・バイナリを表す
} as const;
export type ESampleType = (typeof E_SAMPLE_TYPE)[keyof typeof E_SAMPLE_TYPE]
```

上記の例では```ESampleType```が型を表し、```E_SAMPLE_TYPE```が値を格納するオブジェクトです

比較は下記のようになります

```
if (type === E_SAMPLE_TYPE.BINARY) {
  // BINARYの場合の処理.
  return.
}
```

## 自動出力について

上記Enumについて、PHP側、TypeScript側両方とも積極的に利用すべき機能ですが

言語環境が異なるため二重定義の必要がある点が問題になります。

- PHP側で定義したEnumを参照したいなら同じ値をTypeScript側に定義する必要がある
- PHP側だけ修正して、Enum値が変動してしまう場合にTypeScript側は不具合を発生させる

など、可能であれば避けたい状況です。

テンプレート環境では```php artisan tool:MakeTypescriptEnum```を実行した場合に

PHPの定義ファイルを元にTypeScriptを自動出力するツールを内包しています。

### 基本情報

PHP側がベースとなり、これをTypeScriptへコピーします

下記が必要な条件となるため注意してください

- app/Enum以下に配置する
- enum宣言自体にコメントを付与する
- 各定数の行末尾にコメントを付与する
- メソッドや他コメントは抽出されない

実行権限によりファイルの所有者が期待しないものになる可能性があります。

その場合はchownなどで所有者を適切に変更してください

### 初回出力について

出力先のディレクトリは先に作成しておく必要があります。

- resources/script/Enum/Server/{PHP側と同名のディレクトリ}以下に出力される

### 更新出力について

- ```#ENUM_DEFINE_START#```～```#ENUM_DEFINE_END#```が最新の定義で更新される
- その他の部分は影響を受けない
  - Enumのヘルパ関数は各ファイルに自由に追加してください




