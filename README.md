# btcaddresstocsv

ビットコインのアドレスから取引データをCSV形式で出力するスクリプトです。
CSV形式には日付、数量、その日のビットコインの価格が含まれます。

BTC（ビットコイン）、BCH（ビットコインキャッシュ)、MONA（モナコイン）、ETH（イーサリアム）に対応しています。

# 必要ソフト

php

# 使い方

##ビットコイン(BTC/BCH/MONA)の取引データを出力する場合

`php bitcoincsv.php --coin btc --address ビットコインアドレス --zaifjpyfile zaifの日足のデータ`

zaifディレクトリの中にダウンロードしたzaifの日足データがありますが、
https://zaif.jp/download_trade_price
からダウンロードして最新のデータを利用してください。

##イーサリアム(ETH)の取引データを出力する場合

https://etherscan.io/
から取引データをCSV形式でダウンロードしてください。

`php ethcsv.php --ethcsv 上記のファイル --zaifjpyfile zaifの日足のデータ`



