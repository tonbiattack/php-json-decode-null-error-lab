# PHP `json_decode()` の `null` と構文エラーデバッグラボ

既定の `json_decode()` が、有効なJSON値 `null` と壊れたJSONの復号失敗のどちらにもPHPの `null` を返すため、構文エラーを削除要求として扱ってしまう挙動を再現する。

| 項目 | 内容 |
|---|---|
| PHP | 8.3.6 CLI |
| 外部依存 | なし |
| 再現コマンド | `php tests/run.php` |

初期コミットでは、壊れたJSONを例外として扱うテストが失敗する。修正後は `JSON_THROW_ON_ERROR` を使い、JSON `null` と構文エラーを別の制御フローに分ける。
