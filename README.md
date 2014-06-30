# Gregobase

### Prerequisites
* A working installation of [WordPress](http://wordpress.org/) (used for user management only)
* [gregorio](https://gna.org/projects/gregorio/)
* [LuaLaTeX](http://www.tug.org/texlive/)
* *([ImageMagick](http://www.imagemagick.org/) should not be necessary any more)*
* [PDFCrop](http://pdfcrop.sourceforge.net/)
* [GhostScript](http://www.ghostscript.com/)

### Install
* Copy all GregoBase files into the WordPress directory
* Make sure directories `temp`, `scores`, `scores/pdf`, `scores/png` and `scores/eps` are writeable by the server 
* Rename `include/db.php.sample` in `include/db.php` and edit it to set your database connection data
* Import `gregobase_structure.sql` into your db
* Add an item to your menu linking to`your_wordpress_install/scores.php` or access it directly

If you want to have a local copy of http://gregobase.selapa.net

* Import `grego_online.sql` into your db (of course the user data aren't in that file so you won't see who made the changes)

If you want to be able to personalize your scores

* Install [gregoriophp](https://github.com/jperon/gregoriophp)

### Limitations
* There's no interface for sources edition yet so you need to add them directly into the database
