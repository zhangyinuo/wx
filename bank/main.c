#include <stdio.h>
#include "myconfig.h"

int main(int argc, char **argv)
{
	if(myconfig_init(argc, argv) < 0) 
	{
		printf("myconfig_init fail banker.conf %m\n");
		return -1;
	}

	return 0;
}
