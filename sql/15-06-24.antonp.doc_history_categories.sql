delete from cw_doc_history_categories;
alter table cw_doc_history_categories add index doc_id (doc_id);
alter table cw_doc_history_categories add primary key doc_id_category_id (doc_id, category_id);
replace into cw_doc_history_categories (doc_id, category_id) select cw_docs_items.doc_id, cw_products_categories.category_id from cw_docs_items left join cw_products_categories on cw_products_categories.product_id = cw_docs_items.product_id;
