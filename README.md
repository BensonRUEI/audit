# Audit
## _The Computer Audit Data tool_

Audit 是用來協助整理使用者電腦檢測資料的小工具

## Features

- 匯入與整理使用者電腦IP、單位等資料
- 匯入與整理Nessus弱掃資料
- 依高、中風險列表與統計檢測報告用數據與資料

## Installation

Audit requires ngnix + MySQL + php to run.

請依下列順序安裝

```sh
sh install.sh   安裝相依套件
sh run.sh       執行 nginx、mysql和php 服務
sh db.sh        建立資料庫與資料表
```

執行(在瀏覽器中)

```sh
http://localhost
```


## License

MIT

**Free Software, Hell Yeah!**