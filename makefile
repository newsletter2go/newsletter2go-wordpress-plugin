version = 0_0_00
outfile = Wordpress_nl2go_$(version).zip

$(version): $(outfile)

$(outfile):
	mkdir newsletter2go
	cp -r ./src/* newsletter2go
	zip -r  build.zip ./newsletter2go/*
	mv build.zip $(outfile)
	rm -r newsletter2go

.PHONY: svn
svn:
	cp -r src/* svn/trunk ; \
	cp -r assets/* svn/assets

.PHONY: clean
clean:
	rm -rf tmp
