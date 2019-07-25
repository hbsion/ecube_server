#
# Apache + PHP
#
# 2018-03-15
#   CentOS 7.3 + epel,remi
#   Apache 2.4.6
#   PHP 7.1.15

FROM centos:latest
MAINTAINER te-koyama

# update yum
RUN yum update -y && \
    yum clean all

# epel,remi
RUN yum install -y epel-release && \
	yum install -y http://rpms.famillecollet.com/enterprise/remi-release-7.rpm && \
    yum clean all && \
	sed -i -e "s/enabled *= *1/enabled=0/g" /etc/yum.repos.d/epel.repo && \
	sed -i -e "s/enabled *= *1/enabled=0/g" /etc/yum.repos.d/remi.repo

# httpd, sshd, scp, openssl, sudo, which
RUN yum install -y httpd httpd-tools openssh-server openssh-clients openssl mod_ssl sudo which && \
    yum clean all

# libmcrypt, supervisor
RUN yum install --enablerepo=epel -y libmcrypt supervisor && \
    yum clean all

# gd-last (for php-gd)
RUN yum install --enablerepo=remi -y gd-last && \
    yum clean all

# git
RUN yum install -y git && \
    yum clean all

# php-pecl-memcached
RUN yum install --enablerepo=remi,remi-php71 -y php-pecl-memcached && \
    yum clean all

# php
RUN yum install --enablerepo=remi-php71 -y php php-devel php-gd php-mbstring php-mcrypt php-json php-xml php-opcache && \
    yum clean all && \
	sed -i -e "s/;date.timezone *=.*$/date.timezone = Asia\/Tokyo/" /etc/php.ini && \
    	sed -i -e "s/max_execution_time *=.*$/max_execution_time = 600/" /etc/php.ini && \
    	sed -i -e "s/max_input_time *=.*$/max_input_time = 600/" /etc/php.ini && \
	sed -i -e "s/upload_max_filesize *=.*$/upload_max_filesize = 128M/" /etc/php.ini && \
	sed -i -e "s/post_max_size *=.*$/post_max_size = 128M/" /etc/php.ini

# php-pdo (for mysql)
RUN yum install --enablerepo=remi-php71 -y php-pdo php-mysqlnd php-pecl-mysql && \
    yum clean all

# ec-ceube
RUN yum install --enablerepo=remi-php71 -y php-intl php-zip php-apc && \
    yum clean all

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# phpunit
RUN curl -L https://phar.phpunit.de/phpunit.phar > /usr/local/bin/phpunit && \
	chmod +x /usr/local/bin/phpunit

# initialize for ssh
RUN sed -i '/pam_loginuid\.so/s/required/optional/' /etc/pam.d/sshd && \
	ssh-keygen -A

# create login user
RUN useradd -d /home/sysusr -m -s /bin/bash sysusr && \
	echo sysusr:****sysusr | chpasswd && \
	echo 'sysusr ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

# timezone
RUN cp -p /usr/share/zoneinfo/Japan /etc/localtime

ENV WEBAPP_ROOT /webapp

ADD ./conf/httpd.conf /etc/httpd/conf/httpd.conf
ADD ./conf/supervisord.conf /etc/supervisord.conf

EXPOSE 80 443

CMD ["/usr/bin/supervisord"]
