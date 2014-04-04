#include <stdio.h>
#include <time.h>
#include <stdlib.h>
#include <string.h>
#include "myconfig.h"

enum INDEX {LOCATION = 0, ABSTRACT, INDEX_MAX};

typedef struct
{
	int headcount;
	int linelen;
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
} t_head_init;

typedef struct
{
	int linecount;
	t_base_item *items;
} t_title_init;

#define MAX_RAND 256
#define MAX_STRING 256

static char str_rand[INDEX_MAX][MAX_RAND][MAX_STRING];

static int index_rand[INDEX_MAX][MAX_RAND];

static t_global global;

static t_title_init title;

static t_head_init head;

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

	fprintf(stdout, "%d %s\n", __LINE__, item->msg);

	return 0;
}

static int item_init(char *f, int i, t_base_item **pitem)
{
	char name[128] = {0x0};
	snprintf(name, sizeof(name), "%s_line%d_col_count", f, i);
	fprintf(stdout, "p %d %s\n", __LINE__, name);
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
			fprintf(stdout, "malloc %d %s\n", __LINE__, subname);
			t_base_item * nitem = (t_base_item *) malloc (sizeof(t_base_item));
			if (nitem == NULL)
				return -1;
			memset(nitem, 0, sizeof(t_base_item));

			item->next = nitem;

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
	return 0;
}

static int init_title()
{
	memset(&title, 0, sizeof(title));
	title.linecount = myconfig_get_intval("title_linecount", 4);
	title.items = (t_base_item *) malloc (sizeof(t_base_item) * title.linecount);
	if (title.items == NULL)
		return -1;

	t_base_item * items = title.items;

	int i = 1;
	for (; i <= title.linecount; i++)
	{
		item_init("title", i, &items);
		items++;
	}
	return 0;
}

static int init_head()
{
	memset(&head, 0, sizeof(head));
	head.linecount = myconfig_get_intval("head_linecount", 4);
	head.items = (t_base_item *) malloc (sizeof(t_base_item) * head.linecount);
	if (head.items == NULL)
		return -1;

	t_base_item * items = head.items;

	int i = 1;
	for (; i <= head.linecount; i++)
	{
		item_init("head", i, &items);
		items++;
	}
	items = head.items;
	items++;
	fprintf(stdout, "init %s\n", items->msg);
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
	return ret;
}

static int init_body()
{
	return 0;
}

static int gen_body()
{
	return 0;
}

static int gen_head_page()
{
	return 0;
}

static int print_base_item(int c, t_base_item **items)
{
	t_base_item *item = *items;
	char line[256] = {0x0};
	int i = 0;
	for ( ; i < c; i++)
	{
		memset(line, 32, sizeof(line));
		char *s = line;
		int idx = 0;
		while (1)
		{
			fprintf(stdout, "%d %d addr:%p %p %p %p\n", i, c, &item, &(item->msg), item, item->msg);
			if(item->msg == NULL)
				break;
			s = s + item->spos + idx;
			int span = 0;
			int slen = strlen(item->msg);
			if (strcmp(item->flag, "juzhong") == 0)
				span = (item->len - slen)/2;
			else if (strcmp(item->flag, "juyou") == 0)
				span = item->len - slen;
			s += span;
			if (strcmp(item->msg, "blank"))
				sprintf(s, "%s", item->msg);
			fprintf(stdout, "%d %s %s\n", __LINE__, item->msg, s);
			*(s + slen) = 32;
			idx += item->spos + item->len;
			if (item->next)
				item = item->next;
			else
				break;
		}
		line[127] = 0x0;
		fprintf(stdout, "line:[%s]\n\n", line);
		item++;
	}
	return 0;
}

static int print_bank()
{
	return 0;
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

	if (init_title())
	{
		fprintf(stderr, "init_title err!\n");
		return -1;
	}

	print_base_item(title.linecount, &(title.items));

	if (init_head())
	{
		fprintf(stderr, "init_head err!\n");
		return -1;
	}

	print_base_item(head.linecount, &(head.items));

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

	if (gen_head_page())
	{
		fprintf(stderr, "gen_head_page err!\n");
		return -1;
	}

	print_bank();

	return 0;
}
