This project uses hotlinked Unsplash image URLs for product photos
(see database.sql) so it works immediately after import with zero
setup. If you prefer local images:

1. Save your own product photos into this /images folder.
2. In phpMyAdmin, edit the `image` column of each row in the
   `products` table and replace the URL with a relative path,
   e.g.  images/headphones.jpg
