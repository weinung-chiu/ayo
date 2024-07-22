# API 實作

## 設計原則
依需求敘述，訂單格式檢查與轉換的功能需要在 service 完成，因此假定不同的 service 會各自有不同的規則，
因此在 `OrderRequest` 中只做基本的資料驗證，並在 `OrderService` 來處理訂單的檢查與轉換。並分別寫單元測試來驗證。

## SOLID 與設計模式
* 單一職責原則 (Single Responsibility Principle, SRP)：
    * OrderRequest 負責驗證輸入資料。
    * OrderService 負責處理訂單的檢查與轉換。
    * OrderController 負責處理 HTTP 請求。
* 開放封閉原則 (Open/Closed Principle, OCP)：
    * 可以擴展 OrderService 來增加新的訂單處理邏輯，而不需要修改現有程式。
* 依賴倒置原則 (Dependency Inversion Principle, DIP)：
    * OrderController 依賴於 OrderService 接口而非具體實作，使得 OrderService 可以更換實作方式。

## Run and Test
```bash
# run with docker
docker-compose up -d

# success
curl -i -X POST http://127.0.0.1:8000/api/orders \
     -H "Content-Type: application/json" \
     -d '{
         "id": "A0000001",
         "name": "Melody Holiday Inn",
         "address": {
             "city": "taipei-city",
             "district": "da-an-district",
             "street": "fuxing-south-road"
         },
         "price": 1500,
         "currency": "USD"
     }'
     
 # invalid price
 curl -i -X POST http://127.0.0.1:8000/api/orders \
     -H "Content-Type: application/json" \
     -d '{
         "id": "A0000001",
         "name": "Melody Holiday Inn",
         "address": {
             "city": "taipei-city",
             "district": "da-an-district",
             "street": "fuxing-south-road"
         },
         "price": 15000,
         "currency": "USD"
     }'
```

# 資料庫測驗
目前有一份 MySQL 資料庫，其中的資料表的 ERD 如下圖
- bnbs: 旅宿
    - id旅宿ID
    - name 旅宿名稱
- rooms: 房間
    - id房間ID
    - name 房間名稱
- orders: 訂單
    - id訂單ID
    - bnb_id 旅宿 ID
    - room_id 房間 ID
    - currency 付款幣別，值為:TWD (台幣) , USD (美金) , JPY (日圓)
    - amouont 訂單金額
    - check_in_date 入住日
    - check_out_date 退房日
    - created_at 訂單下訂時間

## 題目一
請寫出一條查詢語句 (SQL)，列出在 2023 年 5 月下訂的訂單，使用台幣付款且5月總金額最 多的前 10 筆的旅宿 ID (bnb_id), 旅宿名稱 (bnb_name), 5 月總金額 (may_amount)

```sql
SELECT 
    b.id AS bnb_id,
    b.name AS bnb_name,
    SUM(o.amount) AS may_amount
FROM 
    orders o
JOIN 
    bnbs b ON o.bnb_id = b.id
WHERE 
    o.currency = 'TWD' 
    AND o.created_at BETWEEN '2023-05-01' AND '2023-05-31'
GROUP BY 
    b.id, b.name
ORDER BY 
    may_amount DESC
LIMIT 10;

```
## 題目二
在題目一的執行下，我們發現 SQL 執行速度很慢，您會怎麼去優化?請闡述您怎麼判斷與優化的方式

- 判斷：利用 EXPLAIN 進一步了解 SQL 如何執行
- 可能的原因與可能的解法
  - Index 設計的改善空間 - 從 schema 下手
      - 在這個語句中，以下的欄位應該要有 index
          - orders.bnb_id
          - orders.created_at
          - orders.currency
      - 很常使用的話，可以依這個 query 事先設計 covering index
          - 為 orders 表建立覆蓋索引：
              - 包含 currency、created_at、bnb_id 和 amount 欄位。
          - 為 bnbs 表建立索引：
              - 包含 id 和 name 欄位。
          - 查詢時資料庫可以利用這些索引來快速檢索資料，從而提升查詢效能。
  - 資料量龐大 - 從 query 的方法下手
      - 分批處理
      - 預先計算並配合 cache
      - 此例中，可以定時計算各 bnb 的 `may_amount` ，需要時僅需依這個值來排序
