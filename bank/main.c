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
	int flag;
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

static int init_title()
{
	memset(&title, 0, sizeof(title));
	title.linecount = myconfig_get_intval("title_linecount", 4);
	title.items = (t_base_item *) malloc (sizeof(t_base_item) * title.linecount);
	if (title.items == NULL)
		return -1;

	int i = 1;
	for (; i <= title.linecount; i++)
	{
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

static int init_head()
{
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

	if (init_head())
	{
		fprintf(stderr, "init_head err!\n");
		return -1;
	}

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
