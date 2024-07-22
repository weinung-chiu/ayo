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
