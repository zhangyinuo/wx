#include <stdio.h>
#include <time.h>
#include <stdlib.h>
#include <string.h>
#include "myconfig.h"
#include "common.h"

FILE *fpout = NULL;

FILE *fplog = NULL;

enum INDEX {LOCATIONIN = 0, ABSTRACTIN, LOCATIONOUT, ABSTRACTOUT, INDEX_MAX};

static char *body_item[] = {"date", "location", "abstract", "in", "out", "balance"};

enum RANGE {DATE = 0, IN, OUT, LASTR};
static char *srange[] = {"date", "in", "out"};

typedef struct
{
	int headcount;
	int linelen;

	int lines_page;
	int total_lines;
	int tail_blank;

	float totalout;
	float totalin;
	float balance_base;

	float jx;
	char *sjx;
	char *sjx_shui;
	char *sjxlocation;

	int page;
} t_global;

typedef struct 
{
	int flag;
	int count;
	int last;
	unsigned char jx[1224];
	float jxf;
} t_soft_global;

static t_soft_global s_global;

static t_global global;
static float last_balance = 0.0;

static int get_jx(int cur, int next)
{
	if (cur == 321 || cur == 621 || cur == 921 || cur == 1221)
	{
		if (s_global.jx[cur])
		{
			if (s_global.jx[cur] <= 0)
				return 0;
			s_global.jx[cur]--;
			return cur;
		}
	}

	if (next == 321 || next == 621 || next == 921 || next == 1221)
	{
		if (s_global.jx[next])
		{
			if (s_global.jx[next] < 0)
				return 0;
			s_global.jx[next]--;
			return next;
		}
	}

	int base = 321;
	for (; base < 1222; base += 300)
	{
		if (base > cur && base < next)
		{
			if (s_global.jx[base] > 0)
			{
				s_global.jx[base]--;
				return base;
			}
			return 0;
		}
	}

	return 0;
}

static int up[16];
static int down[16];
static int avg[16];

typedef struct 
{
	char *msg;
	char *flag;
	int spos;
	int len;
	float r;
	char *next;
}t_base_item;

typedef struct
{
	int count_pos;
	int total_pos;

	char *scount;
	char *stotal;
} t_tail_s;

typedef struct
{
	int flag;
	t_tail_s tail[2];
} t_tail_info;

typedef struct
{
	char *flag;
	int spos;
	int len;
	float r;
}t_body_item;

typedef struct 
{
	int base;
	int totalin;
	int totalout;
	int total_lines;
	int lines_page;

	time_t sday;
	time_t eday;

	int in_s;
	int in_e;

	int out_s;
	int out_e;
	t_base_item *items;
} t_body_init;

typedef struct
{
	int linecount;
	t_base_item *items;
} t_title_init;

typedef struct 
{
	int low;
	int high;
	float r;
} t_base_shouxufei;

typedef struct
{
	int count;
	int max;
	t_base_shouxufei r[100];
} t_shouxufei;

#define MAX_RAND 256
#define MAX_STRING 256

static char str_rand[INDEX_MAX][MAX_RAND][MAX_STRING];

static int index_rand[INDEX_MAX][MAX_RAND];
static int shouxufei_rand[INDEX_MAX][MAX_RAND];
static int max_rand[INDEX_MAX];

static t_title_init title;

static t_title_init head1;

static t_title_init head2;

static t_body_item body[8];

static t_tail_info tail;

static int lastday;

static t_shouxufei shouxufei;

static void print_ab(int l, const char *f)
{
	fprintf(fplog, "%s:%d %s %s %s %s\n", f, l, str_rand[LOCATIONIN][0], str_rand[LOCATIONIN][1], str_rand[LOCATIONIN][2], str_rand[LOCATIONIN][3]);
	fprintf(fplog, "%s:%d %s %s %s %s\n", f, l, str_rand[ABSTRACTIN][0], str_rand[ABSTRACTIN][1], str_rand[ABSTRACTIN][2], str_rand[ABSTRACTIN][3]);
}

static int put_shouxufei(char *v, t_base_shouxufei *r)
{
	char *t = strchr(v, ',');
	if (t == NULL)
		return -1;
	r->low = atoi(v);
	r->high = atoi(t+1);
	t = strchr(v, ':');
	if (t == NULL)
		return -1;
	r->r = atof(t+1);
	return 0;
}

static int init_shouxufei()
{
	memset(&shouxufei, 0, sizeof(shouxufei));
	shouxufei.count = myconfig_get_intval("shouxufei_count", 5);
	shouxufei.max = myconfig_get_intval("shouxufei_max", 100);

	int i = 1;
	for (; i <= shouxufei.count; i++)
	{
		char subname[128] = {0x0};
		snprintf(subname, sizeof(subname), "shouxufei_r%d", i);
		char *v = myconfig_get_value(subname);
		if (v == NULL)
			return -1;
		if (put_shouxufei(v, &(shouxufei.r[i - 1])))
			return -1;
	}
	return 0;
}

static int get_shouxufei(float c, float * r)
{
	int idx = (int) c;
	fprintf(stdout, "%f %d\n", c, idx);
	int i = 0;
	for (; i < shouxufei.count; i++)
	{
		t_base_shouxufei *b = &(shouxufei.r[i]);
		if (b->low <= idx && b->high >= idx)
		{
			*r = b->r;
			if (i == shouxufei.count - 1)
				*r = c * b->r;
			break;
		}
	}
	if (*r >= (float) shouxufei.max)
		*r = (float) shouxufei.max;
	return 0;
}

static int get_item(t_base_item *item, char *v)
{
	char *p = strchr(v, ',');
	if (p == NULL)
		return -1;
	*p = 0x0;

	item->msg = v;
	v = p+1;

	p = strchr(v, ',');
	if (p == NULL)
		return -1;
	*p = 0x0;

	item->flag = v;
	v = p+1;

	item->spos = atoi(v);

	p = strchr(v, ',');
	if (p == NULL)
		return -1;
	item->len = atoi(p+1);

	return 0;
}

static int get_body_format(t_body_item *item, char *v)
{
	char *p = strchr(v, ',');
	if (p == NULL)
		return -1;
	*p = 0x0;

	item->flag = v;
	v = p+1;

	item->spos = atoi(v);

	p = strchr(v, ',');
	if (p == NULL)
		return -1;
	item->len = atoi(p+1);

	v = p+1;
	p = strchr(v, ',');
	if (p == NULL)
		return -1;
	item->r = atof(p+1);

	return 0;
}

static int item_init(char *f, int i, t_base_item **pitem)
{
	char name[128] = {0x0};
	snprintf(name, sizeof(name), "%s_line%d_col_count", f, i);
	int count = myconfig_get_intval(name, 0);
	int c = 1;
	t_base_item *item = *pitem;

	for (; c <= count; c++)
	{
		char subname[128] = {0x0};
		snprintf(subname, sizeof(subname), "%s_%d", name, c);
		char *v = myconfig_get_value(subname);
		if (v == NULL)
			return -1;
		if (item->len)
		{
			t_base_item * nitem = (t_base_item *) malloc (sizeof(t_base_item));
			if (nitem == NULL)
				return -1;
			memset(nitem, 0, sizeof(t_base_item));

			item->next = (char *)nitem;

			item = nitem;
		}

		get_item(item, v);
	}
	return 0;
}

static int init_global()
{
	memset(&global, 0, sizeof(global));
	global.headcount = myconfig_get_intval("headcount", 2);
	global.linelen = myconfig_get_intval("linelen", 128);
	global.lines_page = myconfig_get_intval("lines_page", 40);
	global.total_lines = myconfig_get_intval("total_lines", 145);
	global.tail_blank = myconfig_get_intval("tail_blank", 15);
	char *v = myconfig_get_value("totalout");
	global.totalout = atof(v);

	v = myconfig_get_value("totalin");
	global.totalin = atof(v);

	v = myconfig_get_value("balance_base");
	global.balance_base = atof(v);

	last_balance =  global.balance_base;

	v = myconfig_get_value("lixi_month");
	global.jx = atof(v) / (float)365;
	global.sjx = myconfig_get_value("lixi_name");
	global.sjx_shui = myconfig_get_value("lixi_shui_name");
	global.sjxlocation = myconfig_get_value("lixi_location");
	return 0;
}

static int init_s_global()
{
	memset(&s_global, 0, sizeof(s_global));
	char sday[16] = {0x0};
	snprintf(sday, sizeof(sday), "%d000000", up[DATE]);
	char eday[16] = {0x0};
	snprintf(eday, sizeof(eday), "%d000000", down[DATE]);

	time_t stday = get_time_t(sday);
	time_t etday = get_time_t(eday);

	s_global.last = 0;
	int span = (etday - stday)/86400;
	if (span > global.total_lines)
	{
		s_global.count = span/global.total_lines;
		s_global.flag = 1;
	}
	else
	{
		s_global.count = global.total_lines/span;
		s_global.flag = 0;
	}

	for ( ; stday <= etday; stday += 86400)
	{
		char sbuf[16] = {0x0};
		get_strtime_by_t(sbuf, stday);
		sbuf[8] = 0x0;
		int idx = atoi(sbuf+4);
		if (idx == 321 || idx == 621 || idx == 921 || idx == 1221)
			s_global.jx[idx]++;
	}
	int i = 0;
	for (; i < 1224; i++)
	{
		if (s_global.jx[i])
			fprintf(fplog, "%d %d\n", i, s_global.jx[i]);
	}
	return 0;
}

static int init_tail()
{
	memset(&tail, 0, sizeof(tail));
	tail.flag = myconfig_get_intval("tail_flag", 0);
	tail.tail[0].count_pos = myconfig_get_intval("tail_out_count_pos", 23);
	tail.tail[1].count_pos = myconfig_get_intval("tail_in_count_pos", 23);

	tail.tail[0].total_pos = myconfig_get_intval("tail_out_total_pos", 53);
	tail.tail[1].total_pos = myconfig_get_intval("tail_in_total_pos", 53);

	tail.tail[0].stotal = myconfig_get_value("tail_out_stotal");
	tail.tail[1].stotal = myconfig_get_value("tail_in_stotal");

	tail.tail[0].scount = myconfig_get_value("tail_out_scount");
	tail.tail[1].scount = myconfig_get_value("tail_in_scount");
	return 0;
}

static int init_title(t_title_init *some, char *name)
{
	memset(some, 0, sizeof(t_title_init));

	char subname[128] = {0x0};
	snprintf(subname, sizeof(subname), "%s_linecount", name);
	some->linecount = myconfig_get_intval(subname, 4);
	some->items = (t_base_item *) malloc (sizeof(t_base_item) * some->linecount);
	if (some->items == NULL)
		return -1;
	memset(some->items, 0, sizeof(t_base_item) * some->linecount);

	t_base_item * items = some->items;

	int i = 1;
	for (; i <= some->linecount; i++)
	{
		item_init(name, i, &items);
		items++;
	}
	return 0;
}

static int init_location_or_abstract(char *file, int index)
{
	FILE *fp = fopen(file, "r");
	if (fp == NULL)
	{
		fprintf(fplog, "fopen %s err %m\n", file);
		return -1;
	}

	int ret = 0;
	char (*base)[MAX_STRING];
	base = str_rand[index];
	int idx = 0;
	int sum = 0;
	char buf[256] = {0x0};
	while (fgets(buf, sizeof(buf), fp))
	{
		char *t = strchr(buf, '|');
		if (t == NULL)
			continue;
		*t = 0x0;
		strcpy(base[idx], buf);
		fprintf(fplog, "%s %ld\n", base[idx], strlen(base[idx]));

		int shouxufei = 0;
		char *p = strchr(t+1, '|');
		if (p)
			shouxufei = atoi(p+1);
		int i = 0;
		int c = atoi(t+1);
		for (i = 0; i < c; i++)
		{
			index_rand[index][sum++] = idx;
			max_rand[index]++;
		}
		if (ABSTRACTOUT == index)
			shouxufei_rand[index][idx] = shouxufei;
		idx++;
		memset(buf, 0, sizeof(buf));
	}
	fprintf(fplog, "%d\n",  max_rand[index]);
	fclose(fp);
	return ret;
}

static int init_body()
{
	memset(body, 0, sizeof(body));
	t_body_item * tbody = body;
	int count = sizeof(body_item)/sizeof(char*);
	int i = 0;
	for (; i < count; i++)
	{
		char subname[128] = {0x0};
		snprintf(subname, sizeof(subname), "body_%s_format", body_item[i]);
		char *v = myconfig_get_value(subname);
		if (v == NULL)
			return -1;
		if (get_body_format(tbody, v))
			return -1;
		tbody++;
	}
	return 0;
}

static int gen_body()
{
	memset(up, 0, sizeof(up));
	memset(down, 0, sizeof(down));
	int count = sizeof(srange)/sizeof(char*);
	int i = 0;
	for (; i < count; i++)
	{
		char subname[128] = {0x0};
		snprintf(subname, sizeof(subname), "body_%s_range", srange[i]);
		char *v = myconfig_get_value(subname);
		if (v == NULL)
			return -1;

		char *p = strchr(v, ',');
		if (p == NULL)
			return -1;
		up[i] = atoi(v);
		down[i] = atoi(p+1);
	}
	return 0;
}

static int print_base_item(int c, t_base_item **items)
{
	t_base_item *pitem = *items;
	char *line = (char *) malloc (global.linelen);
	int i = 0;
	for ( ; i < c; i++)
	{
		t_base_item *item = pitem;
		memset(line, 32, global.linelen);
		char *s = line;
		while (1)
		{
			if(item->msg == NULL)
				break;
			s = line + item->spos;
			int span = 0;
			int slen = strlen(item->msg);
			if (strcmp(item->flag, "juzhong") == 0)
				span = (item->len - item->spos - slen)/2;
			else if (strcmp(item->flag, "juyou") == 0)
				span = -slen;
			s += span;
			if (strcmp(item->msg, "blank"))
			{
				char *t = strstr(item->msg, "rpages");
				if (t)
				{
					*t = 0x0;
					sprintf(s, "%s%d", item->msg, global.page);
					*t = 'r';
				}
				else
					sprintf(s, "%s", item->msg);
			}
			*(s + slen) = 32;
			if (item->next)
				item = (t_base_item *)item->next;
			else
				break;
		}
		*(line + global.linelen - 1) = 0x0;
		fprintf(fpout, "%s\r\n", line);
		pitem++;
	}
	free(line);
	return 0;
}

static int print_base_body(int c, t_base_item **items)
{
	t_base_item *pitem = *items;
	char *line = (char *) malloc (global.linelen);
	int i = 0;
	memset(line, 32, global.linelen);
	char *s = line;
	for ( ; i < c; i++)
	{
		t_base_item *item = pitem;
		s = line + item->spos;
		int span = 0;
		int slen = strlen(item->msg);
		if (strcmp(item->flag, "juzhong") == 0)
			span = (item->len - item->spos - slen)/2;
		else if (strcmp(item->flag, "juyou") == 0)
			span = -slen;
		s += span;
		if (strcmp(item->msg, "blank"))
			sprintf(s, "%s", item->msg);
		*(s + slen) = 32;
		pitem++;
	}
	*(line + global.linelen - 1) = 0x0;
	fprintf(fpout, "%s\r\n", line);
	static int g_line = 0;
	g_line++;
	fprintf(fplog, "[%d]:%s\r\n", g_line, line);
//	free(line);
	return 0;
}

static void print_block_line()
{
	int i = 0;
	for (; i < global.linelen; i++)
		fprintf(fpout, "-");
	fprintf(fpout, "\r\n");
}

static void print_block_2line()
{
	int i = 0;
	for (; i < global.linelen; i++)
		fprintf(fpout, "=");
	fprintf(fpout, "\r\n");
}

static void print_head()
{
	global.page++;
	print_base_item(title.linecount, &(title.items));
	print_block_2line();
	print_base_item(head1.linecount, &(head1.items));
	print_block_2line();
	print_base_item(head2.linecount, &(head2.items));
	print_block_line();
}

static void print_jx(char *jxday)
{
	t_base_item items[6];
	memset(items, 0, sizeof(items));
	int i = 0;
	for ( ; i < 6; i++)
	{
		items[i].flag = body[i].flag;
		items[i].len = body[i].len;
		items[i].spos = body[i].spos;
		items[i].r = body[i].r;
	}
	char sonce[16] = {0x0};
	snprintf(sonce, sizeof(sonce), "%0.2f", s_global.jxf);
	global.balance_base += s_global.jxf;
	s_global.jxf = 0.0;
	char balance[16] = {0x0};
	snprintf(balance, sizeof(balance), "%0.2f", global.balance_base);
	items[0].msg = jxday;
	items[1].msg = global.sjxlocation;
	items[2].msg = global.sjx;
	items[3].msg = sonce;
	items[4].msg = "blank";
	items[5].msg = balance;
	t_base_item *item = items;
	print_base_body(6, &item);
	items[2].msg = global.sjx_shui;
	items[3].msg = "blank";
	print_base_body(6, &item);
}

static float print_body(int index, int r)
{
	char lday[16] = {0x0};
	snprintf(lday, sizeof(lday), "%d", lastday);
	int jxday = atoi(lday+4);
	s_global.jxf +=  global.jx * global.balance_base;
	int idx = 0;
	int sxf = 0;
	float once = r%avg[index];
	if (once < up[index])
		once += up[index];
	once += once;
	if (index == IN)
	{
		if (r%7 == 0)
		{
			float yushu = r%100;
			yushu = yushu/100;
			once += yushu;
		}
		global.balance_base += once;
	}
	else
	{
		idx = 2;
		int llr = r%max_rand[idx+0];
		int llindex = index_rand[idx+0][llr];
		sxf = shouxufei_rand[ABSTRACTOUT][llindex];
		fprintf(fplog, "%d %s\n", sxf, str_rand[idx+1][llindex]);
		int zflag = 0;
		if (strstr(str_rand[idx+1][llindex], "现支"))
			zflag = 1;
		if (zflag || r%15)
		{
			if (zflag || r%2 == 0)
			{
				int k = 0;
				if (once > 1000)
				{
					k = once/1000;
					once = k * 1000;
				}
				else
				{
					k = once/100;
					once = k * 100;
				}
			}
			else
				once = (int)once;
		}
		global.balance_base -= once;
	}
	char sonce[16] = {0x0};
	snprintf(sonce, sizeof(sonce), "%0.2f", once);
	char balance[16] = {0x0};
	snprintf(balance, sizeof(balance), "%0.2f", global.balance_base);
	t_base_item items[6];
	memset(items, 0, sizeof(items));
	int i = 0;
	for ( ; i < 6; i++)
	{
		items[i].flag = body[i].flag;
		items[i].len = body[i].len;
		items[i].spos = body[i].spos;
		items[i].r = body[i].r;
	}

	items[0].msg = lday;
	int lr = r%max_rand[idx+0];
	int lindex = index_rand[idx+0][lr];
	items[1].msg = str_rand[idx+0][lindex];

	/*
	int ar = r%max_rand[idx+1];
	int aindex = index_rand[idx+1][ar];
	items[2].msg = str_rand[idx+1][aindex];
	*/
	items[2].msg = str_rand[idx+1][lindex];
	fprintf(fplog, "[%d]:%s %s %s %s\n", lindex, items[1].msg, items[2].msg, str_rand[LOCATIONIN][2], str_rand[LOCATIONIN][3]);
	fprintf(fplog, "[%d]:%s %s %s %s\n", lindex, items[1].msg, items[2].msg, str_rand[ABSTRACTIN][2], str_rand[ABSTRACTIN][3]);
	/*
	   if (flag == 0)
	   {
	   items[1].msg = global.sjxlocation;
	   items[2].msg = global.sjx;
	   }
	   */

	if (index == IN)
	{
		items[3].msg = sonce;
		items[4].msg = "blank";
	}
	else
	{
		items[3].msg = "blank";
		items[4].msg = sonce;
	}

	items[5].msg = balance;

	t_base_item *item = items;
	print_base_body(6, &item);

	if (lastday < down[DATE] + 3)
	{
		char llday[16] = {0x0};
		snprintf(llday, sizeof(llday), "%d000000", lastday);
		time_t cur = get_time_t(llday);
		if (s_global.flag)
			cur += ((r%3) + s_global.count - 1) * 86400;
		else
		{
			if (s_global.last < (s_global.count - 1))
				s_global.last++;
			else
			{
				s_global.last = 0;
				cur += 86400;
			}
		}
		get_strtime_by_t(llday, cur);
		llday[8] = 0x0;
		lastday = atoi(llday);
		int njxday = atoi(llday+4);
		int flag = get_jx(jxday, njxday);
		if (flag > 0)
		{
			snprintf(llday + 4, sizeof(llday) - 4, "%04d", flag);
			print_jx(llday);
		}
	}
	/*
	   if (flag == 0)
	   {
	   items[2].msg = global.sjx_shui;
	   items[3].msg = "blank";
	   print_base_body(6, &item);
	   }
	   */

	if (sxf == 1)
	{
		if (r%2 == 0)
		{
			float ssxf = 0;
			get_shouxufei(once, &ssxf);
			global.balance_base -= ssxf;
			memset(balance, 0, sizeof(balance));
			snprintf(balance, sizeof(balance), "%0.2f", global.balance_base);
			items[5].msg = balance;
			char sssxf[24] = {0x0};
			items[2].msg = "手续费";
			items[3].msg = "blank";
			snprintf(sssxf, sizeof(sssxf), "%0.2f", ssxf);
			items[4].msg = sssxf;
			print_base_body(6, &item);
		}
	}

	return once;
}

static void print_tail()
{
	print_block_2line();
	int i = 0;
	for (; i < global.tail_blank; i++)
		fprintf(fpout, "\r\n");
}

static void print_end(int in, float in_total, int out, float out_total)
{
	print_block_2line();
	char *line = (char *) malloc (global.linelen);
	memset(line, 32, global.linelen);
	*(line + global.linelen - 1) = 0x0;

	char *s = line + tail.tail[0].count_pos;
	int i = sprintf(s, "%s%d", tail.tail[0].scount, out);
	*(s + i) = 32;

	s = line + tail.tail[0].total_pos;
	i = sprintf(s, "%s%0.2f", tail.tail[0].stotal, out_total);
	*(s + i) = 32;

	fprintf(fpout, "%s\r\n", line);

	memset(line, 32, global.linelen);
	*(line + global.linelen - 1) = 0x0;

	s = line + tail.tail[1].count_pos;
	i = sprintf(s, "%s%d", tail.tail[1].scount, in);
	*(s + i) = 32;

	s = line + tail.tail[1].total_pos;
	i = sprintf(s, "%s%0.2f", tail.tail[1].stotal, in_total);
	*(s + i) = 32;

	fprintf(fpout, "%s\r\n", line);
}

static void print_bank()
{
	int i = 0;
	int in = 0;
	int out = 0;

	float in_total = 0;
	float out_total = 0;

	int in_cfg = global.total_lines *2/7;
	int out_cfg = global.total_lines - in_cfg;

	avg[IN] = global.totalin / in_cfg;
	avg[OUT] = global.totalout / out_cfg;
	lastday = up[DATE];

	//for (; i < global.total_lines; i++)
	for (; lastday < down[DATE] + 3; i++)
	{
		if (i > (2*global.total_lines))
			break;
		if (i % global.lines_page == 0)
			print_head();
		srand(time(NULL) + i);
		int bb = 0;
		if (global.balance_base <  4*avg[OUT])
			bb = 1;
		if ((bb - 1 > 1) || time(NULL) > 1405073984)
			break;
		int r = rand();
		if ((r%7) < 2)
		{
			in++;
			in_total += print_body(IN, r);
		}
		else
		{
			out++;
			out_total += print_body(OUT, r);
		}
		if ((i+1) % global.lines_page == 0)
			print_tail();
	}

	print_end(in, in_total, out, out_total);
}

int main(int argc, char **argv)
{
	if(myconfig_init(argc, argv) < 0) 
	{
		printf("myconfig_init fail banker.conf %m\n");
		return -1;
	}

	char *outfile = "./bank.txt";
	fpout = fopen(outfile, "w");
	if (fpout == NULL)
	{
		printf("open %s err %m\n", outfile);
		return -1;
	}

	char *logfile = "./log.txt";
	fplog = fopen(logfile, "w");
	if (fplog == NULL)
	{
		printf("open %s err %m\n", logfile);
		return -1;
	}

	init_global();
	init_tail();

	if (init_shouxufei())
	{
		fprintf(fplog, "init_shouxufei ERR!\n");
		return -1;
	}

	memset(index_rand, 0, sizeof(index_rand));
	memset(str_rand, 0, sizeof(str_rand));
	memset(max_rand, 0, sizeof(max_rand));
	char *location_file = myconfig_get_value("body_locationin_file");
	if (!location_file)
	{
		fprintf(fplog, "need body_locationin_file!\n");
		return -1;
	}

	if (init_location_or_abstract(location_file, LOCATIONIN))
	{
		fprintf(fplog, "init_location_or_abstract err %s %m!\n", location_file);
		return -1;
	}

	print_ab(__LINE__, __FUNCTION__);
	char *abstract_file = myconfig_get_value("body_abstractin_file");
	if (!abstract_file)
	{
		fprintf(fplog, "need body_abstractin_file!\n");
		return -1;
	}

	if (init_location_or_abstract(abstract_file, ABSTRACTIN))
	{
		fprintf(fplog, "init_location_or_abstract err %s %m!\n", abstract_file);
		return -1;
	}

	print_ab(__LINE__, __FUNCTION__);
	location_file = myconfig_get_value("body_locationout_file");
	if (!location_file)
	{
		fprintf(fplog, "need body_locationout_file!\n");
		return -1;
	}

	print_ab(__LINE__, __FUNCTION__);
	if (init_location_or_abstract(location_file, LOCATIONOUT))
	{
		fprintf(fplog, "init_location_or_abstract err %s %m!\n", location_file);
		return -1;
	}

	abstract_file = myconfig_get_value("body_abstractout_file");
	if (!abstract_file)
	{
		fprintf(fplog, "need body_abstractout_file!\n");
		return -1;
	}

	print_ab(__LINE__, __FUNCTION__);
	if (init_location_or_abstract(abstract_file, ABSTRACTOUT))
	{
		fprintf(fplog, "init_location_or_abstract err %s %m!\n", abstract_file);
		return -1;
	}

	print_ab(__LINE__, __FUNCTION__);
	if (init_title(&title, "title"))
	{
		fprintf(fplog, "init_title err!\n");
		return -1;
	}

	print_ab(__LINE__, __FUNCTION__);
//	print_base_item(title.linecount, &(title.items));

	if (init_title(&head1, "head1"))
	{
		fprintf(fplog, "init_head1 err!\n");
		return -1;
	}

	print_ab(__LINE__, __FUNCTION__);
//	print_base_item(head1.linecount, &(head1.items));

	if (init_title(&head2, "head2"))
	{
		fprintf(fplog, "init_head2 err!\n");
		return -1;
	}

//	print_base_item(head2.linecount, &(head2.items));

	print_ab(__LINE__, __FUNCTION__);
	if (init_body())
	{
		fprintf(fplog, "init_body err!\n");
		return -1;
	}

	print_ab(__LINE__, __FUNCTION__);
	if (gen_body())
	{
		fprintf(fplog, "gen_body err!\n");
		return -1;
	}
	print_ab(__LINE__, __FUNCTION__);
	init_s_global();

	print_ab(__LINE__, __FUNCTION__);
	print_bank();

	return 0;
}
