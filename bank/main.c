#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "myconfig.h"

enum INDEX {LOCATION = 0, ABSTRACT, INDEX_MAX};

#define MAX_RAND 256
#define MAX_STRING 256

static char str_rand[INDEX_MAX][MAX_RAND][MAX_STRING];

static int index_rand[INDEX_MAX][MAX_RAND];

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

int main(int argc, char **argv)
{
	if(myconfig_init(argc, argv) < 0) 
	{
		printf("myconfig_init fail banker.conf %m\n");
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

	return 0;
}
