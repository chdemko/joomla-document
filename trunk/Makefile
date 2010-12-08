all:
	cd com_document;				make clean;make;mv com_document-`cat ../VERSION`.zip ..;cd ..
	cd mod_documents_archive;		make clean;make;mv mod_documents_archive-`cat ../VERSION`.zip ..;cd ..
	cd mod_documents_categories;	make clean;make;mv mod_documents_categories-`cat ../VERSION`.zip ..;cd ..
	cd mod_documents_category;		make clean;make;mv mod_documents_category-`cat ../VERSION`.zip ..;cd ..
	cd mod_documents_latest;		make clean;make;mv mod_documents_latest-`cat ../VERSION`.zip ..;cd ..
	cd mod_documents_news;			make clean;make;mv mod_documents_news-`cat ../VERSION`.zip ..;cd ..
	cd mod_documents_popular;		make clean;make;mv mod_documents_popular-`cat ../VERSION`.zip ..;cd ..
	cd plg_content_documentpdf;		make clean;make;mv plg_content_documentpdf-`cat ../VERSION`.zip ..;cd ..
	cd plg_search_document;			make clean;make;mv plg_search_document-`cat ../VERSION`.zip ..;cd ..

clean:
	rm *.zip

