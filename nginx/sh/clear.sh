USER_NAME="you name"
PHPTAR="php-7.2.6.tar.gz"
PHPSRCDIR="php-7.2.6"
PHPVERSION="php726"
PHPHOME="/usr/local/${PHPVERSION}"
PROJECT_LOCATION="/www/"
SITE_LOCATION="${PROJECT_LOCATION}/site/"
SOFT_LOCATION="${PROJECT_LOCATION}/soft/"
PHPSRCLCT="${SOFT_LOCATION}${PHPSRCDIR}"

sudo rm ${PROJECT_LOCATION} -R
sudo rm ${PHPHOME} -R
sudo apt-get remove g++ openssl pkg-config libbz2-dev libssl-dev libxml2-dev libacl1-dev mysql-server-5.7 mysql-client-5.7 gcc nginx libcurl4-openssl-dev libpng12-dev libgd-dev libgmp-dev libedit-dev -y
sudo rm /etc/init.d/${PHPVERSION}-fpm
