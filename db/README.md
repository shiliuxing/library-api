
数据库结构及部分演示数据

```
.
├── library_scheme.sql          // 所有数据表结构
├── library_books.sql           // 图书信息表结构+数据，有一万余本图书信息
├── library_classifications.sql // 图书分类号表结构+数据，包含所有中图法分类号信息
└── library_libraries.sql       // 图书馆表结构+数据，5个图书馆信息
```

为每本图书生成模拟的馆藏数据：

```SQL
INSERT INTO collections (library_id, book_id, total_num, available_num) SELECT a.id, b.id, FLOOR(60 + RAND() * 90), FLOOR(30 + RAND() * 90) FROM libraries a JOIN books b;

# 设置“测试图书馆”的所有馆藏为 0
UPDATE collections set available_num = 0 WHERE library_id = 1
```