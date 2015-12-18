version = 0_0_00
outfile = newsletter2go_v$(version).zip

$(version): $(outfile)

$(outfile): tmp/build.zip
	mv tmp/build.zip $(outfile)

tmp/build.zip: tmp/newsletter2go-for-wordpress
	cd tmp/ && zip -r build.zip newsletter2go-for-wordpress/

tmp/newsletter2go-for-wordpress:
	mkdir -p tmp/newsletter2go-for-wordpress

.PHONY: svn
svn:
	cp -r src/* svn/trunk ; \
	cp -r assets/* svn/assets

.PHONY: clean
clean:
	rm -rf tmp
