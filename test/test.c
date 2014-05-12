#include <stdio.h>
#include <time.h>

int main()
{
	time_t now = time(NULL);
	fprintf(stdout, "%ld\n", now);
	return 0;
}
