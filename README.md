# lwCMS: A lightweight CMS

**lwCMS** was designed to be a lightweight, easy to set up content management system, written in PHP, using MySQL as database; requirements which should be met by most webspaces.

### Setting up lwCMS

lwCMS does not have an installer yet, and therefore must be configured manually. First, missing directories need to be created:
* cache (needs write permission)
* thirdparty
* uploads

A few thirdparty products are needed for lwCMS to work:
* securimage
* CKEditor
* PDW File Browser
* SlidesJS

After the directory structure is set up, the *.sql files need to be imported to the database.
