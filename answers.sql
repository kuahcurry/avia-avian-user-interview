1. 
select
    part_code,
    part_name,
    current_stock,
    minimum_stock,
    minimum_stock-current_stock AS stock_deficit
FROM spare_parts
WHERE current_stock <= minimum_stock
    ORDER BY current_stock ASC;

2. 
SELECT 
    sp.part_code,
    sp.part_name,
    SUM(spu.quantity_used) AS total_usage_30_days,
    SUM(spu.quantity_used) / 30 AS average_usage_per_day,
    MAX(spu.usage_date) AS last_usage_date, 
    sp.current_stock
FROM spare_part_usage spu 
INNER JOIN spare_parts sp ON sp.id = spu.spare_part_id 
WHERE spu.usage_date >= DATE_SUB((SELECT MAX(spu.usage_date)
    FROM spare_part_usage), INTERVAL 30 day)
GROUP BY 
    sp.part_code,
    sp.part_name,
    sp.current_stock
ORDER BY
    total_usage_30_days DESC;