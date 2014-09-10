Installing
==========

Application
-----------
The best way to install is using Composer.

		composer create-project lawondyss/skeleton my-app
		cd my-app

Make directories `temp` and `log` writable. Navigate your browser
to the `www` directory and you will see a welcome page.

It is CRITICAL that whole `app`, `log` and `temp` directories are NOT accessible
directly via a web browser!

Database
--------
For create new database import `/skeletonDump.sql` and in `/app/config/config.neon`
(for localhost `/app/config/config.local.neon`) set access to database.