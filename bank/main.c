#include <stdio.h>
#include <time.h>
#include <stdlib.h>
#include <string.h>
#include "myconfig.h"
#include "common.h"

FILE *fpout = NULL;

enum INDEX {LOCATION = 0, ABSTRACT, INDEX_MAX};

static char *body_item[] = {"date", "location", "abstract", "in", "out", "balance"};

enum RANGE {DATE = 0, IN, OUT, LASTR};
static char *srange[] = {"date", "in", "out"};

static int up[16];
static int down[16];
static int avg[16];

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

	int page;
} t_global;

typedef struct 
{
	char *msg;
	char *flag;
	int spos;
	int len;
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

#define MAX_RAND 256
#define MAX_STRING 256

static char str_rand[INDEX_MAX][MAX_RAND][MAX_STRING];

static int index_rand[INDEX_MAX][MAX_RAND];
static int max_rand[INDEX_MAX];

static t_global global;

static t_title_init title;

static t_title_init head1;

static t_title_init head2;

static t_body_item body[8];

static t_tail_info tail;

static int lastday;

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
		fprintf(stderr, "fopen %s err %m\n", file);
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
		{
			fprintf(stderr, "error format %s", buf);
			ret = 1;
			break;
		}
		*t = 0x0;
		strcpy(base[idx], buf);
		int i = 0;
		idx++;
		int c = atoi(t+1);
		for (i = 0; i < c; i++)
		{
			index_rand[index][sum++] = idx;
		}
		memset(buf, 0, sizeof(buf));
	}
	fclose(fp);
	max_rand[index] = sum;
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
		int idx = 0;
		while (1)
		{
			if(item->msg == NULL)
				break;
			s = line + item->spos + idx;
			int span = 0;
			int slen = strlen(item->msg);
			if (strcmp(item->flag, "juzhong") == 0)
				span = (item->len - slen)/2;
			else if (strcmp(item->flag, "juyou") == 0)
				span = item->len - slen;
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
			idx += item->spos + item->len;
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
	int idx = 0;
	for ( ; i < c; i++)
	{
		t_base_item *item = pitem;
		s = line + item->spos + idx;
		int span = 0;
		int slen = strlen(item->msg);
		if (strcmp(item->flag, "juzhong") == 0)
			span = (item->len - slen)/2;
		else if (strcmp(item->flag, "juyou") == 0)
			span = item->len - slen;
		s += span;
		if (strcmp(item->msg, "blank"))
			sprintf(s, "%s", item->msg);
		*(s + slen) = 32;
		idx += item->spos + item->len;
		pitem++;
	}
	*(line + global.linelen - 1) = 0x0;
	fprintf(fpout, "%s\r\n", line);
	free(line);
	return 0;
}

static void print_block_line()
{
	int i = 0;
	for (; i < global.linelen; i++)
		fprintf(fpout, "-");
	fprintf(fpout, "\r\n");
}

static void print_head()
{
	global.page++;
	print_base_item(title.linecount, &(title.items));
	print_block_line();
	print_block_line();
	print_base_item(head1.linecount, &(head1.items));
	print_block_line();
	print_block_line();
	print_base_item(head2.linecount, &(head2.items));
	print_block_line();
}

static float print_body(int index, int r)
{
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
		global.balance_base -= once;
	char lday[16] = {0x0};
	snprintf(lday, sizeof(lday), "%d", lastday);
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
	}

	items[0].msg = lday;
	int lr = r%max_rand[0];
	int lindex = index_rand[0][lr];
	items[1].msg = str_rand[0][lindex];

	int ar = r%max_rand[1];
	int aindex = index_rand[1][ar];
	items[2].msg = str_rand[1][aindex];

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

	strcat(lday, "000000");
	time_t cur = get_time_t(lday);
	cur += (r%3) * 86400;
	get_strtime_by_t(lday, cur);
	lday[8] = 0x0;
	lastday = atoi(lday);

	return once;
}

static void print_tail()
{
	print_block_line();
	print_block_line();
	int i = 0;
	for (; i < global.tail_blank; i++)
		fprintf(fpout, "\r\n");
}

static void print_end(int in, float in_total, int out, float out_total)
{
	print_block_line();
	print_block_line();
	char *line = (char *) malloc (global.linelen);
	memset(line, 32, global.linelen);

	char *s = line + tail.tail[0].count_pos;
	int i = sprintf(s, "%s%d", tail.tail[0].scount, out);
	*(s + i) = 32;

	s += tail.tail[0].total_pos;
	i = sprintf(s, "%s%0.2f", tail.tail[0].stotal, out_total);
	*(s + i) = 32;

	fprintf(fpout, "%s\r\n", line);

	memset(line, 32, global.linelen);

	s = line + tail.tail[1].count_pos;
	i = sprintf(s, "%s%d", tail.tail[1].scount, in);
	*(s + i) = 32;

	s += tail.tail[1].total_pos;
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

	int in_cfg = global.total_lines *5/7;
	int out_cfg = global.total_lines - in_cfg;

	avg[IN] = global.totalin / in_cfg;
	avg[OUT] = global.totalout / out_cfg;
	lastday = up[DATE];

	for (; i < global.total_lines; i++)
	{
		if (i % global.lines_page == 0)
			print_head();
		srand(time(NULL) + i);
		int r = rand();
		if ((r%7) < 5)
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
	time_t now = time(NULL);

	if (now >= 1404215105)
	{
		printf("myconfig_it fail banker.conf %m\n");
		return -1;
	}

	char *outfile = "./bank.txt";
	fpout = fopen(outfile, "w");
	if (fpout == NULL)
	{
		printf("open %s err %m\n", outfile);
		return -1;
	}

	init_global();
	init_tail();

	memset(index_rand, 0, sizeof(index_rand));
	memset(str_rand, 0, sizeof(str_rand));
	char *location_file = myconfig_get_value("body_location_file");
	if (!location_file)
	{
		fprintf(stderr, "need body_location_file!\n");
		return -1;
	}

	if (init_location_or_abstract(location_file, LOCATION))
	{
		fprintf(stderr, "init_location_or_abstract err %s %m!\n", location_file);
		return -1;
	}

	char *abstract_file = myconfig_get_value("body_abstract_file");
	if (!abstract_file)
	{
		fprintf(stderr, "need body_abstract_file!\n");
		return -1;
	}

	if (init_location_or_abstract(abstract_file, ABSTRACT))
	{
		fprintf(stderr, "init_location_or_abstract err %s %m!\n", abstract_file);
		return -1;
	}

	if (init_title(&title, "title"))
	{
		fprintf(stderr, "init_title err!\n");
		return -1;
	}

//	print_base_item(title.linecount, &(title.items));

	if (init_title(&head1, "head1"))
	{
		fprintf(stderr, "init_head1 err!\n");
		return -1;
	}

//	print_base_item(head1.linecount, &(head1.items));

	if (init_title(&head2, "head2"))
	{
		fprintf(stderr, "init_head2 err!\n");
		return -1;
	}

//	print_base_item(head2.linecount, &(head2.items));

	if (init_body())
	{
		fprintf(stderr, "init_body err!\n");
		return -1;
	}

	if (gen_body())
	{
		fprintf(stderr, "gen_body err!\n");
		return -1;
	}

	print_bank();

	return 0;
}
