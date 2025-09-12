# This example will work with application root directory as docker context
FROM ghcr.io/roadrunner-server/roadrunner:2025 as roadrunner
FROM ghcr.io/spiral/php-grpc:8.4

RUN install-php-extensions pdo_pgsql

WORKDIR /app

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN chmod +x /usr/local/bin/rr

EXPOSE 8080/tcp

CMD /usr/local/bin/rr serve -c .rr.yaml
