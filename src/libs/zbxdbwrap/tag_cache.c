/*
** Copyright (C) 2001-2025 Zabbix SIA
**
** This program is free software: you can redistribute it and/or modify it under the terms of
** the GNU Affero General Public License as published by the Free Software Foundation, version 3.
**
** This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
** without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
** See the GNU Affero General Public License for more details.
**
** You should have received a copy of the GNU Affero General Public License along with this program.
** If not, see <https://www.gnu.org/licenses/>.
**/

#include "tag_cache.h"

#include "zbxdb.h"
#include "zbxnum.h"
#include "zbxstr.h"

void	zbx_db_save_item_template_cache(zbx_uint64_t hostid, zbx_vector_uint64_t *new_itemids)
{
	zbx_db_insert_t	db_insert_item_template_cache_host_itself;

	zbx_db_execute_multiple_query(
			"insert into item_template_cache with recursive cte as ("
				" select i0.templateid, i0.itemid, i0.hostid from items i0"
					" union all"
				" select i1.templateid, c.itemid, c.hostid from cte c"
				" join items i1 on c.templateid=i1.itemid where i1.templateid is not NULL)"
			" select cte.itemid,ii.hostid from cte,items ii"
				" where cte.templateid= ii.itemid and ", "cte.itemid", new_itemids);

	zbx_db_insert_prepare(&db_insert_item_template_cache_host_itself, "item_template_cache",
			"itemid", "link_hostid",  (char *)NULL);

	for (int i = 0; i < new_itemids->values_num; i++)
		zbx_db_insert_add_values(&db_insert_item_template_cache_host_itself, new_itemids->values[i], hostid);

	zbx_db_insert_execute(&db_insert_item_template_cache_host_itself);
	zbx_db_insert_clean(&db_insert_item_template_cache_host_itself);
}

int	zbx_db_delete_host_template_cache(zbx_uint64_t hostid, zbx_vector_uint64_t *del_templateids)
{
	zbx_vector_uint64_t	templateids;
	zbx_db_result_t		result;
	zbx_db_row_t		row;
	size_t			sql_alloc = 256, sql_offset = 0;
	char			*sql = (char *)zbx_malloc(NULL, sql_alloc);
	int			res = SUCCEED;

	zbx_vector_uint64_create(&templateids);

	zbx_strcpy_alloc(&sql, &sql_alloc, &sql_offset,
			"with recursive cte as ("
				" select h0.templateid, h0.hostid from hosts_templates h0"
					" union all"
				" select h1.templateid, c.hostid from cte c"
					" join hosts_templates h1 on c.templateid=h1.hostid)"
			" select templateid from cte where ");

	zbx_db_add_condition_alloc(&sql, &sql_alloc, &sql_offset, "hostid", del_templateids->values,
			del_templateids->values_num);

	if (NULL == (result = zbx_db_select("%s", sql)))
	{
		res = FAIL;
		goto clean;
	}

	while (NULL != (row = zbx_db_fetch(result)))
	{
		zbx_uint64_t	templateid;

		ZBX_STR2UINT64(templateid, row[0]);
		zbx_vector_uint64_append(&templateids, templateid);
	}

	zbx_db_free_result(result);

	for (int i = 0; i < del_templateids->values_num; i++)
		zbx_vector_uint64_append(&templateids, del_templateids->values[i]);

	zbx_free(sql);
	sql_offset = 0;
	sql = (char *)zbx_malloc(NULL, sql_alloc);

	zbx_snprintf_alloc(&sql, &sql_alloc, &sql_offset,
			"delete from host_template_cache"
				" where hostid=" ZBX_FS_UI64
				" and", hostid);

	zbx_db_add_condition_alloc(&sql, &sql_alloc, &sql_offset, "link_hostid",
			templateids.values, templateids.values_num);
	zbx_db_execute("%s", sql);
clean:
	zbx_free(sql);

	return res;
}

int	zbx_db_copy_host_template_cache(zbx_uint64_t hostid, zbx_vector_uint64_t *lnk_templateids)
{
	zbx_vector_uint64_t	templateids;
	zbx_db_result_t		result;
	zbx_db_row_t		row;
	zbx_db_insert_t		db_insert_host_template_cache;
	size_t			sql_alloc = 256, sql_offset = 0;
	char			*sql = (char *)zbx_malloc(NULL, sql_alloc);

	zbx_db_insert_prepare(&db_insert_host_template_cache, "host_template_cache", "hostid", "link_hostid",
			(char *)NULL);

	zbx_vector_uint64_create(&templateids);

	zbx_strcpy_alloc(&sql, &sql_alloc, &sql_offset,
			"with recursive cte as ("
				" select h0.templateid, h0.hostid from hosts_templates h0"
					" union all"
				" select h1.templateid, c.hostid from cte c"
					" join hosts_templates h1 on c.templateid=h1.hostid)"
			" select templateid from cte where");

	zbx_db_add_condition_alloc(&sql, &sql_alloc, &sql_offset, "hostid", lnk_templateids->values,
			lnk_templateids->values_num);

	if (NULL == (result = zbx_db_select("%s", sql)))
		return FAIL;

	while (NULL != (row = zbx_db_fetch(result)))
	{
		zbx_uint64_t	templateid;

		ZBX_STR2UINT64(templateid, row[0]);
		zbx_vector_uint64_append(&templateids, templateid);
	}

	zbx_db_free_result(result);

	for (int i = 0; i < templateids.values_num; i++)
		zbx_db_insert_add_values(&db_insert_host_template_cache, hostid, templateids.values[i]);

	for (int i = 0; i < lnk_templateids->values_num; i++)
		zbx_db_insert_add_values(&db_insert_host_template_cache, hostid,  lnk_templateids->values[i]);

	zbx_db_insert_execute(&db_insert_host_template_cache);
	zbx_db_insert_clean(&db_insert_host_template_cache);

	zbx_vector_uint64_destroy(&templateids);
	zbx_free(sql);

	return SUCCEED;
}

void	zbx_db_save_httptest_template_cache(zbx_uint64_t hostid, zbx_vector_uint64_t *new_httptestids)
{
	zbx_db_insert_t	db_insert_httptest_template_cache_host_itself;

	zbx_db_execute_multiple_query(
			"insert into httptest_template_cache"
				" with recursive cte as ("
					" select i0.templateid, i0.httptestid, i0.hostid from httptest i0"
						" union all"
					" select i1.templateid, c.httptestid, c.hostid from cte c"
						" join httptest i1"
							" on c.templateid=i1.httptestid where"
							" i1.templateid is not null)"
				" select cte.httptestid,ii.hostid from cte,httptest ii where"
					" cte.templateid= ii.httptestid and ",
					" cte.httptestid", new_httptestids);


	zbx_db_insert_prepare(&db_insert_httptest_template_cache_host_itself, "httptest_template_cache",
			"httptestid", "link_hostid",  (char *)NULL);

	for (int i = 0; i < new_httptestids->values_num; i++)
	{
		zbx_db_insert_add_values(&db_insert_httptest_template_cache_host_itself, new_httptestids->values[i],
				hostid);
	}

	zbx_db_insert_execute(&db_insert_httptest_template_cache_host_itself);
	zbx_db_insert_clean(&db_insert_httptest_template_cache_host_itself);
}
