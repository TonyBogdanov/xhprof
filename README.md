```bash
cd ~/
mkdir xhprof
cd xhprof
git clone https://github.com/tideways/php-xhprof-extension.git .
phpize
./configure
make
sudo make install
```

```bash
echo "extension=tideways_xhprof.so" >> php.ini
service apache2 restart
```

```bash
composer require --dev tonybogdanov/xhprof
```

```php
\Profiler\Profiler::registerPublicPath( 'public/xhprof', 'http://localhost/xhprof' );
\Profiler\Profiler::start();

// Code being profiled.

\Profiler\Profiler::stop();
exit;
```
