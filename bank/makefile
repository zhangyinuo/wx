NS=banker
CC=gcc
CXX=g++
INC+= 
LIB+= -lpthread -ldl -lrt -rdynamic 
TARGET=$(NS)
ALL:$(TARGET)

CFLAGS+= -g -O2 -pipe -Wall -fno-strict-aliasing -D_GNU_SOURCE -D_REENTRANT -I../

OBJ= main.o	\
	myconfig.o

$(TARGET):$(OBJ)
	$(CC)	$(CFLAGS) -o $@ $^ $(LIB)
#	install $(TARGET) ../bin
%.o: %.cpp
	$(CXX)  $(CFLAGS) $(INC) -c -o $@ $< 	 
%.o: %.c
	$(CC)  $(CFLAGS) $(INC) -c -o $@ $< 	 
clean:
	rm -f $(OBJ)
	rm -f $(TARGET) 	
	rm -f core.*
