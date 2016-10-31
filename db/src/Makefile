CXXFLAGS=-std=c++11 -MMD -g -I /usr/local/include
LDLIBS= -L /usr/local/lib 
LDFLAGS= -lpcrecpp -lmql -lemdf
DEPFILES=$(OBJFILES:.o=.d)

search_gr:	search_gr.o emdros_iterators.o
	g++ $(CXXFLAGS) $(LDLIBS) -o $@ $+ $(LDFLAGS)

search_heb:	search_heb.o emdros_iterators.o
	g++ $(CXXFLAGS) $(LDLIBS) -o $@ $+ $(LDFLAGS)

-include $(DEPFILES)
