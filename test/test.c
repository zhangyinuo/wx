#include <stdio.h>

int main()
{
	char buf[8] = {0x0};
	int i = 921;
	snprintf(buf, sizeof(buf), "%04d", i);
	fprintf(stderr, "%s\n", buf);
	return 0;
}
