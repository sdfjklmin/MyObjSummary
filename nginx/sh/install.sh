##### set install shell
#define variable
USER_NAME="ubuntu"
SERVER_USER="www-data"
PHPTAR="php-7.2.6.tar.gz"
PHPSRCDIR="php-7.2.6"
PHPVERSION="php726"
PHPHOME="/usr/local/${PHPVERSION}"
PROJECT_LOCATION="/www/"
PROJECT_NAME="app"
SITE_LOCATION="${PROJECT_LOCATION}/site/"
SOFT_LOCATION="${PROJECT_LOCATION}/soft/"
EXT_LOCATION="${PROJECT_LOCATION}/ext/"
PHPSRCLCT="${SOFT_LOCATION}${PHPSRCDIR}"
CODE_SYNC_CMD="git clone git:address"
DOWNLOAD_DIR="/tmp/"

cd ~/download
#wget http://hk1.php.net/get/${PHPTAR}/from/this/mirror -O ${PHPTAR}
sudo mkdir ${SITE_LOCATION} -p
sudo chown ${USER_NAME} ${SITE_LOCATION} -R
sudo mkdir ${SOFT_LOCATION} -p
sudo chown ${USER_NAME} ${SOFT_LOCATION}
sudo mkdir ${PHPHOME}
sudo chown ${USER_NAME} ${PHPHOME}
sudo mkdir ${EXT_LOCATION} -p
sudo chown ${USER_NAME} ${EXT_LOCATION} -R

cp ~/download/${PHPTAR} ${SOFT_LOCATION}
cd ${SOFT_LOCATION}
tar zxf ${PHPTAR}
cd ${PHPSRCDIR}
sudo apt-get install gcc g++ libacl1-dev libxml2-dev libssl-dev openssl libssl-dev pkg-config libbz2-dev libcurl4-openssl-dev libpng12-dev libgd-dev libgmp-dev libedit-dev -y
./configure --prefix=${PHPHOME} --htmldir=${PHPHOME}/doc --with-config-file-path=${PHPHOME} --enable-fpm --with-fpm-user=www-data --with-fpm-group=www-data --with-fpm-acl --with-litespeed --enable-phpdbg --enable-phpdbg-webhelper --enable-phpdbg-debug --enable-debug --enable-sigchild --disable-short-tags --with-openssl --with-libxml-dir --with-system-ciphers --with-pcre-regex --with-pcre-jit --with-zlib --enable-bcmath --with-bz2 --enable-calendar --with-curl --enable-exif --with-gd --enable-ftp --with-freetype-dir --enable-gd-jis-conv --with-gettext --with-gmp --with-mhash --enable-intl --enable-mbstring --enable-pcntl --with-pdo-mysql --with-libedit --with-readline --enable-shmop --enable-soap --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-zip --enable-mysqlnd --with-pear
make
sudo make install
wget http://pear.php.net/go-pear.phar
sudo ${PHPHOME}/bin/php go-pear.phar
sudo chmod +x ${PHPSRCLCT}/sapi/fpm/init.d.php-fpm
sudo ln -s ${PHPSRCLCT}/sapi/fpm/init.d.php-fpm /etc/init.d/${PHPVERSION}-fpm
cd ${SITE_LOCATION}
${CODE_SYNC_CMD}
sudo mkdir ${SITE_LOCATION}${PROJECT_NAME}/runtime
sudo chown ${SERVER_USER} ${SITE_LOCATION}${PROJECT_NAME}/runtime
cd ${EXT_LOCATION} && ${PHPHOME}/bin/php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" && ${PHPHOME}/bin/php composer-setup.php
sudo apt-get install nginx -y
sudo apt-get install  mysql-client-5.7 -y
