FROM 192.168.5.112/library/php7_3swool:latest
COPY . /var/www/html

WORKDIR /var/www/html
EXPOSE 9527
ENTRYPOINT ["./entrypoint.sh"]
CMD ["bin/server", "start"]