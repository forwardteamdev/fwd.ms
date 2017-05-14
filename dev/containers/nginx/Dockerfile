FROM hope/nginx:1.12

COPY rootfs/app.conf /etc/nginx/conf.d/

RUN \
    echo -e "pid /var/run/nginx.pid;\n" >> ${NGINX_CONFIG} && \
    rm -f /etc/nginx/conf.d/default.conf

ENTRYPOINT ["nginx", "-c", "/etc/nginx/nginx.conf", "-g", "daemon off;"]

EXPOSE 80
