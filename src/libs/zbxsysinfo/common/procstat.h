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

#ifndef ZABBIX_PROCSTAT_H
#define ZABBIX_PROCSTAT_H

#include "config.h"

#ifdef ZBX_PROCSTAT_COLLECTOR

#include "zbxalgo.h"
#include "zbxtypes.h"

#define ZBX_PROCSTAT_CPU_USER			0x01
#define ZBX_PROCSTAT_CPU_SYSTEM			0x02
#define ZBX_PROCSTAT_CPU_TOTAL			(ZBX_PROCSTAT_CPU_USER | ZBX_PROCSTAT_CPU_SYSTEM)

#define ZBX_PROCSTAT_FLAGS_ZONE_CURRENT		0
#define ZBX_PROCSTAT_FLAGS_ZONE_ALL		1

/* process cpu utilization data */
typedef struct
{
	pid_t		pid;

	/* errno error code */
	int		error;

	zbx_uint64_t	utime;
	zbx_uint64_t	stime;

	/* process start time, used to validate if the old */
	/* snapshot data belongs to the same process       */
	zbx_uint64_t	starttime;
}
zbx_procstat_util_t;

void	zbx_procstat_init(void);
void	zbx_procstat_destroy(void);
int	zbx_procstat_collector_started(void);
int	zbx_procstat_get_util(const char *procname, const char *username, const char *cmdline, zbx_uint64_t flags,
		int period, int type, double *value, char **errmsg);
void	zbx_procstat_collect(void);

/* external functions used by procstat collector */
int	zbx_proc_get_processes(zbx_vector_ptr_t *processes, unsigned int flags);
void	zbx_proc_get_matching_pids(const zbx_vector_ptr_t *processes, const char *procname, const char *username,
		const char *cmdline, zbx_uint64_t flags, zbx_vector_uint64_t *pids);
void	zbx_proc_get_process_stats(zbx_procstat_util_t *procs, int procs_num);
void	zbx_proc_free_processes(zbx_vector_ptr_t *processes);

#endif	/* ZBX_PROCSTAT_COLLECTOR */

#endif	/* ZABBIX_PROCSTAT_H */
