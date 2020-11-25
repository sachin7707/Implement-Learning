SELECT o.*, c.*, p.* FROM implement_learninginstitute_backend.orders AS o
LEFT JOIN companies AS c ON o.id = c.order_id
LEFT JOIN participants AS p ON p.company_id = c.id

WHERE o.id = 9200